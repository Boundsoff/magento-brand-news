<?php

namespace Boundsoff\BrandNews\Model;

use Boundsoff\BrandNews\Api\FeedbackServiceInterface;
use Boundsoff\BrandNews\Model\Exception\FeedbackServiceException;
use Boundsoff\BrandNews\Model\Exception\BlogFeedConsumerException;
use Composer\InstalledVersions;
use DateTime;
use Laminas\Feed\Reader\Reader;
use Laminas\Http\Client;
use Magento\AdminNotification\Model\InboxFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\FlagManager;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;

class FeedbackService implements FeedbackServiceInterface, ArgumentInterface
{
    private const ADDED_MESSAGES_HASH_FLAG_CODE = 'bf__added_messages_hash';
    private const BLOG_URL_RSS_FEED = 'https://boundsoff.com/news/rss';

    /**
     * @param InboxFactory $inboxFactory
     * @param FlagManager $flagManager
     * @param TimezoneInterface $timezone
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        protected readonly InboxFactory         $inboxFactory,
        protected readonly FlagManager          $flagManager,
        protected readonly TimezoneInterface    $timezone,
        protected readonly ScopeConfigInterface $scopeConfig,
    ) {
    }

    /**
     * @inheritdoc
     */
    public function isEnabled(
        ConfigEnableOptions $option,
        $scopeType = ScopeConfigInterface::SCOPE_TYPE_DEFAULT,
        $scopeCode = null,
    ): bool {
        return $this->scopeConfig->getValue($option->value);
    }

    /**
     * @inheritdoc
     */
    public function addAdminNotification(string $title, string $description, string $url, string $timeout = ''): void
    {
        if (empty($title)) {
            throw FeedbackServiceException\Codes::EmptyTitle->getException();
        }

        if (empty($description)) {
            throw FeedbackServiceException\Codes::EmptyDescription->getException();
        }

        if (empty($url)) {
            throw FeedbackServiceException\Codes::EmptyUrl->getException();
        }

        if (filter_var($url, FILTER_VALIDATE_URL) === false) {
            throw FeedbackServiceException\Codes::InvalidUrl->getException();
        }

        if (!$this->isUriAvailable($url)) {
            throw FeedbackServiceException\Codes::ResponseCodeInvalid->getException();
        }

        $timestamp = $this->getTimestamp(sha1("{$title}{$description}{$url}"));
        if (!empty($timeout)) {
            $scheduleAt = $this->timezone->date($timestamp)
                ->modify($timeout);

            $isInverted = !!$this->timezone->date()
                ->diff($scheduleAt)
                ->invert;

            if ($isInverted) {
                return;
            }
        }

        $this->inboxFactory->create()
            ->addNotice($title, $description, $url);
    }

    /**
     * @inheritdoc
     */
    public function readBlogFeed(?DateTime $fromDate = null): array
    {
        if (empty($fromDate)) {
            $fromDate = $this->timezone->date('-1 month');
        }
        $blogUrl = self::BLOG_URL_RSS_FEED;
        $blogUrl .= '?' . http_build_query(['fromTimestamp' => $fromDate->format('Y-m-d')]);

        if (!$this->isUriAvailable($blogUrl)) {
            throw BlogFeedConsumerException\Codes::FeedUrlNotFound->getException();
        }

        $feeds = [];
        foreach (Reader::import($blogUrl) as $feed) {
            $feeds[] = $feed;
        }
        return $feeds;
    }

    /**
     * @inheritdoc
     */
    public function getModulesUpdated(): array
    {
        if (!$this->isEnabled(ConfigEnableOptions::MarketplaceUpdates)) {
            return [];
        }

        $packages = InstalledVersions::getInstalledPackages();
        $packages = array_filter($packages, fn($package) => str_contains($package, 'boundsoff/'));

        return array_map(fn ($package) => [
            'name' => $package,
            'version' => InstalledVersions::getPrettyVersion($package),
        ], $packages);
    }

    /**
     * Getting timestamp for given hash from flag messages
     *
     * @param string $hash
     * @return int
     */
    protected function getTimestamp(string $hash): int
    {
        $flagData = $this->flagManager->getFlagData(static::ADDED_MESSAGES_HASH_FLAG_CODE) ?? [];
        if (!isset($flagData[$hash])) {
            $flagData[$hash] = $this->timezone->date()
                ->getTimestamp();
            $this->flagManager->saveFlag(static::ADDED_MESSAGES_HASH_FLAG_CODE, $flagData);
        }

        return $flagData[$hash];
    }

    /**
     * @inheritdoc
     */
    public function isUriAvailable(string $uri): bool
    {
        return (new Client())
            ->setUri($uri)
            ->getResponse()
            ->isOk();
    }
}

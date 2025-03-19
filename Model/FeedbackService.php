<?php

namespace Boundsoff\BrandNews\Model;

use Boundsoff\BrandNews\Api\FeedbackServiceInterface;
use Boundsoff\BrandNews\Model\Exception\FeedbackServiceException;
use Magento\AdminNotification\Model\InboxFactory;
use Magento\Framework\FlagManager;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;

class FeedbackService implements FeedbackServiceInterface
{
    protected const ADDED_MESSAGES_HASH_FLAG_CODE = 'added_messages_hash';

    /**
     * @param InboxFactory $inboxFactory
     * @param FlagManager $flagManager
     * @param TimezoneInterface $timezone
     */
    public function __construct(
        protected readonly InboxFactory $inboxFactory,
        protected readonly FlagManager $flagManager,
        protected readonly TimezoneInterface $timezone,
    ) {
    }

    /**
     * @inheritdoc
     */
    public function add(string $title, string $description, string $url, string $timeout = ''): void
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

        // phpcs:ignore
        $headers = get_headers($url);
        if (!$headers || !str_contains($headers[0], '200')) {
            throw FeedbackServiceException\Codes::ResponseCodeInvalid->getException([
                'status_code' => $headers[0],
            ]);
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
}

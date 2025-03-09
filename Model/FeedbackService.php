<?php

namespace Boundsoff\BrandNews\Model;

use Boundsoff\BrandNews\Api\FeedbackServiceInterface;
use Boundsoff\BrandNews\Model\Exception\FeedbackServiceException;
use Magento\AdminNotification\Model\InboxFactory;
use Magento\Framework\FlagManager;

class FeedbackService implements FeedbackServiceInterface
{
    protected const ADDED_MESSAGES_HASH_FLAG_CODE = 'added_messages_hash';

    /**
     * @param InboxFactory $inboxFactory
     * @param FlagManager $flagManager
     */
    public function __construct(
        protected readonly InboxFactory $inboxFactory,
        protected readonly FlagManager $flagManager,
    ) {
    }

    /**
     * @inheritdoc
     */
    public function add(string $title, string $description, string $url): void
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

        $hash = sha1("{$title}:{$description}:{$url}");
        $flagData = $this->flagManager->getFlagData(static::ADDED_MESSAGES_HASH_FLAG_CODE) ?? [];
        if (in_array($hash, $flagData)) {
            return;
        }

        $this->inboxFactory->create()
            ->addNotice($title, $description, $url);

        $flagData[] = $hash;
        $this->flagManager->saveFlag(static::ADDED_MESSAGES_HASH_FLAG_CODE, $flagData);
    }
}

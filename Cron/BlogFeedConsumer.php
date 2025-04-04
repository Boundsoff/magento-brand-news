<?php

namespace Boundsoff\BrandNews\Cron;

use Boundsoff\BrandNews\Api\FeedbackServiceInterface;
use Boundsoff\BrandNews\Model\ConfigEnableOptions;
use Boundsoff\BrandNews\Model\Exception\BlogFeedConsumerException;
use Magento\Framework\FlagManager;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;

class BlogFeedConsumer
{
    protected const TIMESTAMP_FEED_LAST_UPDATE_FLAG_CODE = 'bf__brand_news_feed_timestamp';

    /**
     * @param FeedbackServiceInterface $feedbackService
     * @param FlagManager $flagManager
     * @param TimezoneInterface $timezone
     */
    public function __construct(
        protected readonly FeedbackServiceInterface $feedbackService,
        protected readonly FlagManager              $flagManager,
        protected readonly TimezoneInterface        $timezone,
    ) {
    }

    /**
     * Consume feed and add ass notification
     *
     * @return void
     * @throws BlogFeedConsumerException
     * @throws \Boundsoff\BrandNews\Model\Exception\FeedbackServiceException
     * @throws \DateMalformedStringException
     */
    public function execute(): void
    {
        if (!$this->feedbackService->isEnabled(ConfigEnableOptions::BlogFeed)) {
            return;
        }

        $timestamp = $this->flagManager->getFlagData(static::TIMESTAMP_FEED_LAST_UPDATE_FLAG_CODE);
        if (!empty($timestamp)) {
            $timestamp = $this->timezone->date($timestamp, useTimezone: false, includeTime: false);
        }

        $entries = $this->feedbackService->readBlogFeed($timestamp);
        array_walk($entries, fn($entry) => $this->feedbackService->addAdminNotification(
            $entry->getTitle(),
            $entry->getDescription(),
            $entry->getLink(),
        ));

        $timestamp = $this->timezone->date(useTimezone: false, includeTime: false)
            ->getTimestamp();
        $this->flagManager->saveFlag(static::TIMESTAMP_FEED_LAST_UPDATE_FLAG_CODE, $timestamp);
    }
}

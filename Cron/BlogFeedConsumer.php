<?php

namespace Boundsoff\BrandNews\Cron;

use Boundsoff\BrandNews\Api\FeedbackServiceInterface;
use Boundsoff\BrandNews\Model\ConfigEnableOptions;
use Boundsoff\BrandNews\Model\Exception\BlogFeedConsumerException;
use Laminas\Feed\Reader\Reader;

class BlogFeedConsumer
{
    protected const FEED_URL = 'https://boundsoff.com/feed/atom';

    /**
     * @param FeedbackServiceInterface $feedbackService
     */
    public function __construct(
        protected readonly FeedbackServiceInterface $feedbackService,
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
    }
}

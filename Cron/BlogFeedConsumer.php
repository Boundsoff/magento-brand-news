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

        // phpcs:ignore
        $headers = get_headers(static::FEED_URL);
        if (!$headers || !str_contains($headers[0], '200')) {
            throw BlogFeedConsumerException\Codes::FeedUrlNotFound->getException([
                'status_code' => $headers[0],
            ]);
        }

        // @todo filter out from last sync date
        $feeds = Reader::import(static::FEED_URL);
        foreach ($feeds as $feed) {
            $title = $feed->getTitle();
            $description = $feed->getDescription();
            $link = $feed->getLink();

            $this->feedbackService->add($title, $description, $link);
        }
    }
}

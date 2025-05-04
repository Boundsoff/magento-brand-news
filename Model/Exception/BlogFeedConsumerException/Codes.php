<?php

namespace Boundsoff\BrandNews\Model\Exception\BlogFeedConsumerException;

use Boundsoff\BrandNews\Model\Exception\BlogFeedConsumerException;

enum Codes: int
{
    case FeedUrlNotFound = 1;

    /**
     * Get exception class to be thrown
     *
     * @param array $context
     * @return BlogFeedConsumerException
     */
    public function getException(array $context = []): BlogFeedConsumerException
    {
        return (match ($this) {
            self::FeedUrlNotFound => new BlogFeedConsumerException(__('Feed is not active.'), code: $this->value),
        })
            ->setContext($context);
    }
}

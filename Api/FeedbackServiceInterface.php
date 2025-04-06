<?php

namespace Boundsoff\BrandNews\Api;

use Boundsoff\BrandNews\Model\ConfigEnableOptions;
use Boundsoff\BrandNews\Model\Exception\BlogFeedConsumerException;
use Boundsoff\BrandNews\Model\Exception\FeedbackServiceException;
use DateMalformedStringException;
use DateTime;
use Laminas\Feed\Reader\Entry\EntryInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;

interface FeedbackServiceInterface
{
    /**
     * Add new feed information to the notifications
     *
     * @param string $title
     * @param string $description
     * @param string $url
     * @return void
     * @throws FeedbackServiceException
     * @throws DateMalformedStringException
     */
    public function addAdminNotification(string $title, string $description, string $url): void;

    /**
     * Get entries from configured blog rss feed
     *
     * @param DateTime|null $fromDate
     * @return EntryInterface[]
     * @throws BlogFeedConsumerException
     */
    public function readBlogFeed(?DateTime $fromDate = null): array;
}

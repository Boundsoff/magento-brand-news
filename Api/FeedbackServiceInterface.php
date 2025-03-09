<?php

namespace Boundsoff\BrandNews\Api;

use Boundsoff\BrandNews\Model\Exception\FeedbackServiceException;

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
     */
    public function add(string $title, string $description, string $url): void;
}

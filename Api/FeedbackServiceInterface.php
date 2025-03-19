<?php

namespace Boundsoff\BrandNews\Api;

use Boundsoff\BrandNews\Model\Exception\FeedbackServiceException;
use DateMalformedStringException;

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
    public function add(string $title, string $description, string $url): void;
}

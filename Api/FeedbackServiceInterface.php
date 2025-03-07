<?php

namespace Boundsoff\BrandNews\Api;

interface FeedbackServiceInterface
{
    /**
     * Add new feed information to the notifications
     *
     * @param string $title
     * @param string $description
     * @param string $url
     * @return void
     */
    public function add(string $title, string $description, string $url = ''): void;
}

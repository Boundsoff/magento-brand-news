<?php

namespace Boundsoff\BrandNews\Api;

use Boundsoff\BrandNews\Model\Exception\BlogFeedConsumerException;
use Boundsoff\BrandNews\Model\Exception\MenuNotificationException;

interface MenuNotificationServiceInterface
{

    /**
     * Get the count for menu notifications
     *
     * @return array{id: string, count: int}
     */
    public function getCounter(): array;

    /**
     * Adjust the notification count for given menu
     *
     * @param string $menuId ex. $notifications[$menuId]
     * @param int $count
     * @return void
     * @throws BlogFeedConsumerException
     * @throws MenuNotificationException
     */
    public function adjustCounter(string $menuId, int $count): void;
}

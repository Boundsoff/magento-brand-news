<?php

namespace Boundsoff\BrandNews\Model\Exception\MenuNotificationException;

use Boundsoff\BrandNews\Model\Exception\BlogFeedConsumerException;
use Boundsoff\BrandNews\Model\Exception\MenuNotificationException;

enum Codes: int
{
    case MenuItemDoesNotExists = 1;
    case MenuItemIsNotAtRootLevel = 2;

    /**
     * Get exception class to be thrown
     *
     * @param array $context
     * @return BlogFeedConsumerException
     */
    public function getException(array $context = []): MenuNotificationException
    {
        return (match ($this) {
            self::MenuItemDoesNotExists => new MenuNotificationException(
                __('Given menu item does not exists.'),
                code: $this->value,
            ),
            self::MenuItemIsNotAtRootLevel => new MenuNotificationException(
                __('Menu item is not at root level.'),
                code: $this->value,
            ),
        })
            ->setContext($context);
    }
}

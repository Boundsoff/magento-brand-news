<?php

namespace Boundsoff\BrandNews\Model;

use Boundsoff\BrandNews\Api\MenuNotificationServiceInterface;
use Boundsoff\BrandNews\Model\Exception\MenuNotificationException;
use Magento\Backend\Model\Menu\Config as MenuConfig;
use Magento\Framework\FlagManager;
use Magento\Framework\View\Element\Block\ArgumentInterface;

class MenuNotificationService implements MenuNotificationServiceInterface, ArgumentInterface
{
    public const MENU_NOTIFICATIONS_FLAG_CODE = 'bf__menu_notifications';

    /**
     * @param FlagManager $flagManager
     */
    public function __construct(
        protected readonly FlagManager $flagManager,
        protected readonly MenuConfig  $menuConfig,
    ) {
    }

    /**
     * @ingeritdoc
     */
    public function getCounter(): array
    {
        $counter = [];
        $notifications = $this->flagManager->getFlagData(static::MENU_NOTIFICATIONS_FLAG_CODE) ?? [];
        foreach ($notifications as $menuId => $count) {
            if (empty($this->menuConfig->getMenu()->get($menuId))) {
                continue;
            }

            if (!empty($this->menuConfig->getMenu()->getParentItems($menuId))) {
                continue;
            }

            if (!is_numeric($count)) {
                continue;
            }

            $counter[] = ['id' => $menuId, 'count' => $count];
        }

        return $counter;
    }

    /**
     * @ingeritdoc
     */
    public function adjustCounter(string $menuId, string $hash, bool $increase): void
    {
        if (empty($this->menuConfig->getMenu()->get($menuId))) {
            throw MenuNotificationException\Codes::MenuItemDoesNotExists->getException([
                'menu' => $menuId,
            ]);
        }

        if (!empty($this->menuConfig->getMenu()->getParentItems($menuId))) {
            throw MenuNotificationException\Codes::MenuItemIsNotAtRootLevel->getException([
                'menu' => $menuId,
            ]);
        }

        $notifications = $this->getCounter();
        $notifications = array_column($notifications, 'count', 'id');
        if ($increase) {
            $notifications[$menuId][$hash] = 1;
        } else {
            unset($notifications[$menuId][$hash]);
        }

        $this->flagManager->saveFlag(static::MENU_NOTIFICATIONS_FLAG_CODE, $notifications);
    }
}

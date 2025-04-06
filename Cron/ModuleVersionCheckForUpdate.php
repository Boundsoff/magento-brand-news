<?php

namespace Boundsoff\BrandNews\Cron;

use Boundsoff\BrandNews\Api\FeedbackServiceInterface;
use Boundsoff\BrandNews\Api\MenuNotificationServiceInterface;
use Boundsoff\BrandNews\Api\ModuleInfoServiceInterface;
use Boundsoff\BrandNews\Helper\Data as Helper;
use Boundsoff\BrandNews\Model\ConfigEnableOptions;
use Psr\Log\LoggerInterface;

class ModuleVersionCheckForUpdate
{
    /**
     * @param ModuleInfoServiceInterface $moduleInfoService
     * @param MenuNotificationServiceInterface $notificationService
     * @param LoggerInterface $logger
     * @param Helper $helper
     */
    public function __construct(
        protected readonly ModuleInfoServiceInterface       $moduleInfoService,
        protected readonly MenuNotificationServiceInterface $notificationService,
        protected readonly LoggerInterface                  $logger,
        protected readonly Helper                           $helper,
    ) {
    }

    /**
     * Check the modules version and adjust notification badge count
     *
     * @return void
     */
    public function execute(): void
    {
        if (!$this->helper->isEnabled(ConfigEnableOptions::MarketplaceUpdates)) {
            return;
        }

        $brandModules = $this->moduleInfoService->getVersions();
        foreach ($brandModules as $module) {
            try {
                $increase = version_compare($module['version'], $module['version_available'], '>');
                $hash = sha1("moduleUpdate.{$module['name']}}");

                $this->notificationService->adjustCounter('Magento_Marketplace::partners', $hash, $increase);
            } catch (\Throwable $exception) {
                $this->logger->error($exception->getMessage());
                $this->logger->debug($exception->getTraceAsString());
            }
        }
    }
}

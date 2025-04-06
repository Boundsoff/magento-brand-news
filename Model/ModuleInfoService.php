<?php

namespace Boundsoff\BrandNews\Model;

use Boundsoff\BrandNews\Api\ModuleInfoServiceInterface;
use Boundsoff\BrandNews\Helper\Data as Helper;
use Composer\InstalledVersions;
use Magento\Framework\View\Element\Block\ArgumentInterface;

class ModuleInfoService implements ModuleInfoServiceInterface, ArgumentInterface
{
    private const URL_BRAND_MODULES = 'https://boundsoff.com/marketplace/versions.csv';

    /**
     * @param Helper $helper
     */
    public function __construct(
        protected readonly Helper $helper,
    ) {
    }

    /**
     * @inheritdoc
     */
    public function getVersions(string $packagePrefix = 'boundsoff/'): array
    {
        if (!$this->helper->isEnabled(ConfigEnableOptions::MarketplaceUpdates)) {
            return [];
        }

        $packages = InstalledVersions::getInstalledPackages();
        $packages = array_filter($packages, fn($package) => str_contains($package, $packagePrefix));

        $versionsAvailable = $this->helper->getResponseBody(static::URL_BRAND_MODULES) ?? [];
        if (!empty($versionsAvailable)) {
            $versionsAvailable = explode("\n", $versionsAvailable);
            $versionsAvailable = array_map(fn($line) => str_getcsv($line), $versionsAvailable);
            $versionsAvailable = array_column($versionsAvailable, 1, 0);
        }

        return array_map(fn($package) => [
            'name' => $package,
            'version' => InstalledVersions::getPrettyVersion($package),
            'version_available' => $versionsAvailable[$package] ?? '0.0.0',
        ], $packages);
    }
}

<?php

namespace Boundsoff\BrandNews\Test\Unit;

use Boundsoff\BrandNews\Helper\Data as Helper;
use Boundsoff\BrandNews\Model\ModuleInfoService;
use PHPUnit\Framework\TestCase;

class ModuleInfoServiceTest extends TestCase
{
    protected const CSV_VERSION_CONTENT = <<<CSV
"magento/module-catalog","100.0.0"
"magento/module-catalog-inventory","100.0.0"
CSV;

    public function testGetVersions()
    {
        $helper = $this->createMock(Helper::class);

        $helper->expects($this->once())
            ->method('isEnabled')
            ->willReturn(true);

        $helper->expects($this->once())
            ->method('getResponseBody')
            ->willReturn(static::CSV_VERSION_CONTENT);

        $service = new ModuleInfoService($helper);
        $modules = $service->getVersions('magento/module-catalog');
        $modules = array_column($modules, null, 'name');

        $this->assertArrayHasKey('magento/module-catalog', $modules);
        $this->assertArrayHasKey('magento/module-catalog-inventory', $modules);
        $this->assertArrayHasKey('magento/module-catalog-url-rewrite', $modules);

        $this->assertArrayNotHasKey('boundsoff/module-brand-news', $modules);
        $this->assertEquals('100.0.0', $modules['magento/module-catalog']['version_available']);
        $this->assertEquals('100.0.0', $modules['magento/module-catalog-inventory']['version_available']);
        $this->assertEquals('0.0.0', $modules['magento/module-catalog-url-rewrite']['version_available']);
    }
}

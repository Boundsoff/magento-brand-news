<?php

namespace Boundsoff\BrandNews\Block\Adminhtml;

use Boundsoff\BrandNews\Api\FeedbackServiceInterface;
use Boundsoff\BrandNews\Api\ModuleInfoServiceInterface;
use Boundsoff\BrandNews\Helper\Data as Helper;
use Boundsoff\BrandNews\Model\ConfigEnableOptions;
use Magento\Backend\Block\Template;

/**
 * @method ModuleInfoServiceInterface getModuleInfoService()
 * @method string getMarketplaceUri()
 * @method Helper getDataHelper()
 */
class Marketplace extends Template
{
    protected function _toHtml()
    {
        if (!$this->getDataHelper()->isEnabled(ConfigEnableOptions::MarketplaceEnabled)) {
            return '';
        }

        return parent::_toHtml();
    }
}

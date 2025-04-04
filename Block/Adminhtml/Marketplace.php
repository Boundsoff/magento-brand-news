<?php

namespace Boundsoff\BrandNews\Block\Adminhtml;

use Boundsoff\BrandNews\Api\FeedbackServiceInterface;
use Boundsoff\BrandNews\Model\ConfigEnableOptions;
use Magento\Backend\Block\Template;

/**
 * @method FeedbackServiceInterface getFeedbackService()
 */
class Marketplace extends Template
{
    protected function _toHtml()
    {
        if (!$this->getFeedbackService()->isEnabled(ConfigEnableOptions::MarketplaceEnabled)) {
            return '';
        }

        return parent::_toHtml();
    }
}

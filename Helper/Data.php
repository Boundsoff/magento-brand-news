<?php

namespace Boundsoff\BrandNews\Helper;

use Boundsoff\BrandNews\Model\ConfigEnableOptions;
use Laminas\Http\Client;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;

class Data implements ArgumentInterface
{
    /**
     * @param ScopeConfigInterface $scopeConfig
     * @param Client $client
     */
    public function __construct(
        protected readonly ScopeConfigInterface $scopeConfig,
        protected readonly Client $client = new Client(),
    ) {
    }

    /**
     * Check if given chanel is enabled
     *
     * @param ConfigEnableOptions $option
     * @param string $scopeType
     * @param int|string|null $scopeCode
     * @return bool
     */
    public function isEnabled(
        ConfigEnableOptions $option,
                            $scopeType = ScopeConfigInterface::SCOPE_TYPE_DEFAULT,
                            $scopeCode = null,
    ): bool {
        return $this->scopeConfig->getValue($option->value, $scopeType, $scopeCode);
    }

    /**
     * Checking if given external service is responding
     *
     * @param string $uri
     * @return bool
     */
    public function isUriAvailable(string $uri): bool
    {
        return $this->client
            ->setUri($uri)
            ->getResponse()
            ->isOk();
    }

    /**
     * Get body response of the url or null of is not good
     *
     * @param string $uri
     * @return string|null
     */
    public function getResponseBody(string $uri): ?string
    {
        $response = $this->client->setUri($uri)
            ->getResponse();

        if (!$response->isOk()) {
            return null;
        }

        return $response->getBody();
    }
}

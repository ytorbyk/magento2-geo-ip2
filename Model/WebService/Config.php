<?php
/**
 * Copyright © 2015 ToBai. All rights reserved.
 */

namespace Tobai\GeoIp2\Model\WebService;

/**
 * WebService config model
 */
class Config
{
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ) {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @return string
     */
    public function getUserId()
    {
        return $this->scopeConfig->getValue('tobai_geoip2/web_service/user_id');
    }

    /**
     * @return string
     */
    public function getLicenseKey()
    {
        return $this->scopeConfig->getValue('tobai_geoip2/web_service/license_key');
    }

    /**
     * @return string
     */
    public function getHost()
    {
        return $this->scopeConfig->getValue('tobai_geoip2/web_service/host');
    }
}


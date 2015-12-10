<?php
/**
 * Copyright © 2015 ToBai. All rights reserved.
 */

namespace Tobai\GeoIp2\Model\Database;

use Magento\Framework\App\Config\ScopeConfigInterface;

/**
 * Config model
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
    public function __construct(ScopeConfigInterface $scopeConfig) {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @return array
     */
    public function getAvailableDatabases()
    {
        $dbCodes = $this->scopeConfig->getValue('tobai_geoip2/database/available');
        return !empty($dbCodes) ? (array)explode(",", $dbCodes) : [];
    }

    /**
     * @return bool
     */
    public function isAutoUpdate()
    {
        return $this->scopeConfig->isSetFlag('tobai_geoip2/database/auto_update');
    }
}

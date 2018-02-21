<?php
/**
 * Copyright © 2015 ToBai. All rights reserved.
 */

namespace Tobai\GeoIp2\Model\WebService;

use Magento\Framework\ObjectManagerInterface;

/**
 * Database client factory
 */
class ClientFactory
{
    /**
     * @var \Tobai\GeoIp2\Model\WebService\Config
     */
    protected $config;

    /**
     * @param \Tobai\GeoIp2\Model\WebService\Config $config
     */
    public function __construct(
        \Tobai\GeoIp2\Model\WebService\Config $config
    ) {
        $this->config = $config;
    }

    /**
     * @param array $data
     * @return \GeoIp2\WebService\Client
     */
    public function create(array $data = [])
    {
        if (isset($data['options']) && is_string($data['options'])) {
            $data['options'] = ['host' => $data['options']];
        } elseif (empty($data['options'])) {
            $data['options'] = [];
        }

        $userId = !empty($data['userId']) ? $data['userId'] : $this->config->getUserId();
        $licenseKey = !empty($data['licenseKey']) ? $data['licenseKey'] : $this->config->getLicenseKey();
        $locales = !empty($data['locales']) ? $data['locales'] : ['en'];
        $options = $data['options'] + ['host' => $this->config->getHost()];

        $client = new \GeoIp2\WebService\Client($userId, $licenseKey, $locales, $options);
        return $client;
    }
}

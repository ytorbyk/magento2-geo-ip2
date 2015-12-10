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
     * @var ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * Instance name to create
     *
     * @var string
     */
    protected $instanceName;

    /**
     * @var \Tobai\GeoIp2\Model\WebService\Config
     */
    protected $config;

    /**
     * @param \Tobai\GeoIp2\Model\WebService\Config $config
     * @param ObjectManagerInterface $objectManager
     * @param string $instanceName
     */
    public function __construct(
        Config $config,
        ObjectManagerInterface $objectManager,
        $instanceName = 'GeoIp2\WebService\Client'
    ) {
        $this->config = $config;
        $this->objectManager = $objectManager;
        $this->instanceName = $instanceName;
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

        $data['options'] += $this->getDefaultOptions();
        $data = $data + $this->getConfigCredentials();

        $client = $this->objectManager->create($this->instanceName, $data);

        if (!$client instanceof \GeoIp2\WebService\Client) {
            throw new \InvalidArgumentException(get_class($client) . ' must be an instance of \GeoIp2\WebService\Client.');
        }

        return $client;
    }

    /**
     * @return array
     */
    protected function getConfigCredentials()
    {
        return [
            'userId' => $this->config->getUserId(),
            'licenseKey' => $this->config->getLicenseKey()
        ];
    }

    protected function getDefaultOptions()
    {
        return ['host' => $this->config->getHost()];
    }
}

<?php
/**
 * Copyright © 2015 ToBai. All rights reserved.
 */

namespace Tobai\GeoIp2\Model\Database\Updater;

/**
 * Update selected db in configuration
 */
class Selected
{
    /**
     * @var \Tobai\GeoIp2\Model\Database\Config
     */
    protected $config;

    /**
     * @var \Tobai\GeoIp2\Model\Database\UpdaterInterface
     */
    protected $updater;

    /**
     * @param \Tobai\GeoIp2\Model\Database\Config $config
     * @param \Tobai\GeoIp2\Model\Database\UpdaterInterface $updater
     */
    public function __construct(
        \Tobai\GeoIp2\Model\Database\Config $config,
        \Tobai\GeoIp2\Model\Database\UpdaterInterface $updater
    ) {
        $this->config = $config;
        $this->updater = $updater;
    }

    /**
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function update()
    {
        $dbCodes = $this->config->getAvailableDatabases();

        foreach ($dbCodes as $dbCode) {
            $this->updater->update($dbCode);
        }
    }
}

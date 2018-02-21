<?php
/**
 * Copyright © 2015 ToBai. All rights reserved.
 */

namespace Tobai\GeoIp2\Cron;

/**
 * Class Observer
 */
class UpdateDb
{
    /**
     * @var \Tobai\GeoIp2\Model\Database\Updater\Selected
     */
    protected $updaterSelected;

    /**
     * @var \Tobai\GeoIp2\Model\Database\Config
     */
    protected $databaseConfig;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    /**
     * @param \Tobai\GeoIp2\Model\Database\Updater\Selected $updaterSelected
     * @param \Tobai\GeoIp2\Model\Database\Config $databaseConfig
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(
        \Tobai\GeoIp2\Model\Database\Updater\Selected $updaterSelected,
        \Tobai\GeoIp2\Model\Database\Config $databaseConfig,
        \Psr\Log\LoggerInterface $logger
    ) {
        $this->updaterSelected = $updaterSelected;
        $this->databaseConfig = $databaseConfig;
        $this->logger = $logger;
    }

    /**
     * @return $this
     */
    public function execute()
    {
        if (!$this->databaseConfig->isAutoUpdate()) {
            return $this;
        }

        try {
            $this->updaterSelected->update();
        } catch (\Exception $e) {
            $this->logger->critical($e);
        }

        return $this;
    }
}

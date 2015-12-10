<?php
/**
 * Copyright © 2015 ToBai. All rights reserved.
 */

namespace Tobai\GeoIp2\Cron;

use Tobai\GeoIp2\Model\Database;
use Psr\Log\LoggerInterface as Logger;

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
     * @var Logger
     */
    protected $logger;

    /**
     * @param \Tobai\GeoIp2\Model\Database\Updater\Selected $updaterSelected
     * @param \Tobai\GeoIp2\Model\Database\Config $databaseConfig
     * @param Logger $logger
     */
    public function __construct(
        Database\Updater\Selected $updaterSelected,
        Database\Config $databaseConfig,
        Logger $logger
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

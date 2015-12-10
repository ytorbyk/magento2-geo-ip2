<?php
/**
 * Copyright © 2015 ToBai. All rights reserved.
 */

namespace Tobai\GeoIp2\Model\Database;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\ObjectManagerInterface;
use Tobai\GeoIp2\Model\Database;

/**
 * Database reader factory
 */
class ReaderFactory
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
     * @var \Tobai\GeoIp2\Model\Database
     */
    protected $database;

    /**
     * @param \Tobai\GeoIp2\Model\Database $database
     * @param ObjectManagerInterface $objectManager
     * @param string $instanceName
     */
    public function __construct(
        Database $database,
        ObjectManagerInterface $objectManager,
        $instanceName = 'GeoIp2\Database\Reader'
    ) {
        $this->database = $database;
        $this->objectManager = $objectManager;
        $this->instanceName = $instanceName;
    }

    /**
     * @param string $dbCode
     * @param array $locales
     * @return \GeoIp2\Database\Reader
     * @throws LocalizedException
     */
    public function create($dbCode, array $locales = ['en'])
    {
        if (!$this->database->isDbAvailable($dbCode)) {
            throw new LocalizedException(__('GeoIp2 database with "%1" code is not declared.', $dbCode));
        }

        $reader = $this->objectManager->create(
            $this->instanceName,
            ['filename' => $this->database->getDbPath($dbCode, true), 'locales' => $locales]
        );

        if (!$reader instanceof \GeoIp2\Database\Reader) {
            throw new \InvalidArgumentException(get_class($reader) . ' must be an instance of \GeoIp2\Database\Reader.');
        }

        return $reader;
    }
}

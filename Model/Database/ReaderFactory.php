<?php
/**
 * Copyright © 2015 ToBai. All rights reserved.
 */

namespace Tobai\GeoIp2\Model\Database;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\ObjectManagerInterface;

/**
 * Database reader factory
 */
class ReaderFactory
{
    /**
     * @var \Tobai\GeoIp2\Model\Database
     */
    protected $database;

    /**
     * @param \Tobai\GeoIp2\Model\Database $database
     */
    public function __construct(
        \Tobai\GeoIp2\Model\Database $database
    ) {
        $this->database = $database;
    }

    /**
     * @param string $dbCode
     * @param array $locales
     * @return \GeoIp2\Database\Reader
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function create($dbCode, array $locales = ['en'])
    {
        if (!$this->database->isDbAvailable($dbCode)) {
            throw new LocalizedException(__('GeoIp2 database with "%1" code is not declared.', $dbCode));
        }

        $reader = new \GeoIp2\Database\Reader(
            $this->database->getDbPath($dbCode, true),
            $locales
        );

        return $reader;
    }
}

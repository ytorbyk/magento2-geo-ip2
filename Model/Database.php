<?php
/**
 * Copyright © 2015 ToBai. All rights reserved.
 */

namespace Tobai\GeoIp2\Model;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;

/**
 * Database model
 */
class Database
{
    /**
     * Databases directory in var
     */
    const BASE_DIR = 'tobai-geoip';

    /**
     * @var \Magento\Framework\Filesystem\Directory\WriteInterface
     */
    protected $directory;

    /**
     * Database file names
     *
     * @var array
     */
    protected $databases;

    /**
     * Database remote url
     *
     * @var string
     */
    protected $location;

    /**
     * @param \Magento\Framework\Filesystem $filesystem
     * @param array $databases
     */
    public function __construct(
        Filesystem $filesystem,
        array $databases
    ) {
        $this->directory = $filesystem->getDirectoryWrite(DirectoryList::VAR_DIR);
        $this->databases = $databases;
    }

    /**
     * Retrieve databases directory absolute path
     *
     * @return string
     */
    public function getBasePath()
    {
        return $this->directory->getAbsolutePath(self::BASE_DIR);
    }

    /**
     * @param string $dbCode
     * @return string|bool
     */
    public function getDbFileName($dbCode)
    {
        return !empty($this->databases[$dbCode]) ? $this->databases[$dbCode] : false;
    }

    /**
     * @param string $dbCode
     * @param bool $absolute
     * @return bool|string
     */
    public function getDbPath($dbCode, $absolute = false)
    {
        $baseDir = $absolute ? $this->getBasePath() : self::BASE_DIR;
        return $this->getDbFileName($dbCode)
            ? $baseDir . DIRECTORY_SEPARATOR . $this->getDbFileName($dbCode)
            : false;
    }

    /**
     * @param string $dbCode
     * @return bool
     */
    public function isDbAvailable($dbCode)
    {
        $dbPath = $this->getDbPath($dbCode);
        return $dbPath && $this->directory->isFile($dbPath) && $this->directory->isReadable($dbPath);
    }

    /**
     * @param string $dbCode
     * @param string|null $statCode
     * @return array|bool|mixed
     */
    public function getDbFileStat($dbCode, $statCode = null)
    {
        $stat = $this->isDbAvailable($dbCode) ? $this->directory->stat($this->getDbPath($dbCode)) : false;
        return !empty($stat) ? ($statCode ? $stat[$statCode] : $stat) : false;
    }
}

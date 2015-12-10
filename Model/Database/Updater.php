<?php
/**
 * Copyright © 2015 ToBai. All rights reserved.
 */

namespace Tobai\GeoIp2\Model\Database;

use Tobai\GeoIp2\Model\Database;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;
use Magento\Framework\Archive\ArchiveInterface;
use Magento\Framework\Exception\LocalizedException;

/**
 * Update db from remote server
 */
class Updater implements UpdaterInterface
{
    /**
     * @var \Magento\Framework\Filesystem\Directory\WriteInterface
     */
    protected $directory;

    /**
     * @var \Magento\Framework\Archive\ArchiveInterface
     */
    protected $archive;

    /**
     * @var \Tobai\GeoIp2\Model\Database
     */
    protected $database;

    /**
     * Database remote url
     *
     * @var string
     */
    protected $dbLocation;

    /**
     * @var string
     */
    protected $dbArchiveExt;

    /**
     * @param string $dbLocation
     * @param \Tobai\GeoIp2\Model\Database $database
     * @param \Magento\Framework\Filesystem $filesystem
     * @param \Magento\Framework\Archive\ArchiveInterface $archive
     * @param string $dbArchiveExt
     */
    public function __construct(
        $dbLocation,
        Database $database,
        Filesystem $filesystem,
        ArchiveInterface $archive,
        $dbArchiveExt = ''
    ) {
        $this->dbLocation = $dbLocation;
        $this->database = $database;
        $this->directory = $filesystem->getDirectoryWrite(DirectoryList::VAR_DIR);
        $this->archive = $archive;
        $this->dbArchiveExt = $dbArchiveExt;
    }

    /**
     * @param string $dbCode
     * @throws LocalizedException
     */
    public function update($dbCode)
    {
        $this->createBaseDir();

        if (!$this->database->getDbFileName($dbCode)) {
            throw new LocalizedException(__('Database with "%1" code is not declared.', $dbCode));
        }

        $this->downloadDb($dbCode);
        $this->unpackDb($dbCode);
        $this->deletePackDb($dbCode);
    }

    /**
     * @throws LocalizedException
     */
    protected function createBaseDir()
    {
        if (!$this->directory->create(Database::BASE_DIR)) {
            throw new LocalizedException(__('Cannot create db directory.'));
        }
    }

    /**
     * @param string $dbCode
     * @throws LocalizedException
     */
    protected function downloadDb($dbCode)
    {
        $contents = $this->loadDbContent($this->getDbUrl($dbCode));

        $this->directory->writeFile($this->getDbArchiveFilePath($dbCode), $contents);

        if (!$this->directory->isExist($this->getDbArchiveFilePath($dbCode))) {
            throw new LocalizedException(__('Cannot save db file.'));
        }
    }

    /**
     * @param string $dbUrl
     * @return string
     */
    protected function loadDbContent($dbUrl)
    {
        return stream_get_contents(fopen($dbUrl, 'r', false));
    }

    /**
     * @param string $dbCode
     * @return string
     */
    protected function getDbUrl($dbCode)
    {
        return str_replace('%db_name%', $this->database->getDbFileName($dbCode), $this->dbLocation);
    }

    /**
     * @param string $dbCode
     * @return string
     */
    protected function getDbArchiveFilePath($dbCode)
    {
        return $this->database->getDbPath($dbCode) . $this->dbArchiveExt;
    }

    /**
     * @param string $dbCode
     * @throws LocalizedException
     */
    protected function unpackDb($dbCode)
    {
        $this->archive->unpack(
            $this->directory->getAbsolutePath($this->getDbArchiveFilePath($dbCode)),
            $this->directory->getAbsolutePath($this->database->getDbPath($dbCode))
        );

        if (!$this->directory->isExist($this->database->getDbPath($dbCode))) {
            throw new LocalizedException(__('Cannot unpack db file.'));
        }
    }

    /**
     * @param string $dbCode
     */
    protected function deletePackDb($dbCode)
    {
        $this->directory->delete($this->getDbArchiveFilePath($dbCode));
    }
}

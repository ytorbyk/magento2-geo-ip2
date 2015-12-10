<?php
/**
 * Copyright © 2015 ToBai. All rights reserved.
 */

namespace Tobai\GeoIp2\Test\Unit\Model\Database;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager as ObjectManagerHelper;
use Magento\Framework\App\Filesystem\DirectoryList;
use Tobai\GeoIp2\Model\Database;

/**
 * Class UpdaterTest
 */
class UpdaterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Tobai\GeoIp2\Model\Database\Updater
     */
    protected $updater;

    /**
     * @var \Magento\Framework\Filesystem\Directory\WriteInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $directory;

    /**
     * @var \Magento\Framework\Archive\ArchiveInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $archive;

    /**
     * @var \Tobai\GeoIp2\Model\Database|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $database;

    /**
     * @var string
     */
    protected $dbLocation = 'some-remote-url-%db_name%-file';

    /**
     * @var string
     */
    protected $dbArchiveExt = '.some_arch_ext';

    protected function setUp()
    {
        $this->directory = $this->getMock('Magento\Framework\Filesystem\Directory\WriteInterface');

        $filesystem = $this->getMockBuilder('Magento\Framework\Filesystem')
            ->disableOriginalConstructor()
            ->getMock();
        $filesystem->expects($this->once())
            ->method('getDirectoryWrite')
            ->with(DirectoryList::VAR_DIR)
            ->willReturn($this->directory);

        $this->database = $this->getMockBuilder('Tobai\GeoIp2\Model\Database')
            ->disableOriginalConstructor()
            ->getMock();

        $this->archive = $this->getMockBuilder('Magento\Framework\Archive\ArchiveInterface')
            ->disableOriginalConstructor()
            ->getMock();

        $objectManagerHelper = new ObjectManagerHelper($this);
        $constructArguments = $objectManagerHelper->getConstructArguments(
            'Tobai\GeoIp2\Model\Database\Updater',
            [
                'dbLocation' => $this->dbLocation,
                'dbArchiveExt'=> $this->dbArchiveExt,
                'filesystem' => $filesystem,
                'database' => $this->database,
                'archive' => $this->archive
            ]
        );

        $this->updater = $this->getMockBuilder('Tobai\GeoIp2\Model\Database\Updater')
            ->setConstructorArgs($constructArguments)
            ->setMethods(['loadDbContent'])
            ->getMock();
    }

    /**
     * @expectedException \Magento\Framework\Exception\LocalizedException
     * @expectedExceptionMessage Cannot create db directory.
     */
    public function testUpdateCannotCreateBaseDir()
    {
        $dbCode = 'some-db-code';

        $this->configureCreateBaseDirMethod(false);

        $this->updater->update($dbCode);
    }

    /**
     * @expectedException \Magento\Framework\Exception\LocalizedException
     * @expectedExceptionMessage Database with "some-db-code" code is not declared.
     */
    public function testUpdateWithNotDeclaredDb()
    {
        $dbCode = 'some-db-code';

        $this->configureCreateBaseDirMethod();
        $this->configureGetDbFileNameMethod($dbCode, false);

        $this->updater->update($dbCode);
    }

    public function testUpdate()
    {
        $dbCode = 'some-db-code';

        $dbFileName = $dbCode . '.mmd';
        $dbPath = 'some-db-local-path';
        $dbUrl = "some-remote-url-{$dbFileName}-file";

        $dbArchPath = $dbPath . $this->dbArchiveExt;
        $dbPathAbsolute = 'path-to-dir' . $dbPath;
        $dbArchPathAbsolute = 'path-to-dir' . $dbArchPath;

        $dbContent = 'loaded content db';

        $this->configureCreateBaseDirMethod();
        $this->configureGetDbFileNameMethod($dbCode, $dbFileName);
        $this->configureDownload($dbCode, $dbPath, $dbArchPath, $dbUrl, $dbContent);
        $this->configureUnpack($dbPath, $dbArchPath, $dbPathAbsolute, $dbArchPathAbsolute);

        $this->directory->expects($this->exactly(2))
            ->method('isExist')
            ->willReturnMap([
                [$dbArchPath, true],
                [$dbPath, true]
            ]);

        $this->directory->expects($this->once())
            ->method('delete')
            ->with($dbArchPath);

        $this->updater->update($dbCode);
    }

    /**
     * @expectedException \Magento\Framework\Exception\LocalizedException
     * @expectedExceptionMessage Cannot save db file.
     */
    public function testUpdateCannotSaveFile()
    {
        $dbCode = 'some-db-code';

        $dbFileName = $dbCode . '.mmd';
        $dbPath = 'some-db-local-path';
        $dbUrl = "some-remote-url-{$dbFileName}-file";

        $dbArchPath = $dbPath . $this->dbArchiveExt;

        $dbContent = 'loaded content db';

        $this->configureCreateBaseDirMethod();
        $this->configureGetDbFileNameMethod($dbCode, $dbFileName);
        $this->configureDownload($dbCode, $dbPath, $dbArchPath, $dbUrl, $dbContent);

        $this->directory->expects($this->once())
            ->method('isExist')
            ->willReturnMap([
                [$dbArchPath, false],
            ]);

        $this->updater->update($dbCode);
    }

    /**
     * @expectedException \Magento\Framework\Exception\LocalizedException
     * @expectedExceptionMessage  Cannot unpack db file.
     */
    public function testUpdateCannotSaveUnpackedFile()
    {
        $dbCode = 'some-db-code';

        $dbFileName = $dbCode . '.mmd';
        $dbPath = 'some-db-local-path';
        $dbUrl = "some-remote-url-{$dbFileName}-file";

        $dbArchPath = $dbPath . $this->dbArchiveExt;
        $dbPathAbsolute = 'path-to-dir' . $dbPath;
        $dbArchPathAbsolute = 'path-to-dir' . $dbArchPath;

        $dbContent = 'loaded content db';

        $this->configureCreateBaseDirMethod();
        $this->configureGetDbFileNameMethod($dbCode, $dbFileName);
        $this->configureDownload($dbCode, $dbPath, $dbArchPath, $dbUrl, $dbContent);
        $this->configureUnpack($dbPath, $dbArchPath, $dbPathAbsolute, $dbArchPathAbsolute);

        $this->directory->expects($this->exactly(2))
            ->method('isExist')
            ->willReturnMap([
                [$dbArchPath, true],
                [$dbPath, false]
            ]);

        $this->updater->update($dbCode);
    }

    /**
     * @param bool $isCreated
     */
    protected function configureCreateBaseDirMethod($isCreated = true)
    {
        $this->directory->expects($this->once())
            ->method('create')
            ->with(Database::BASE_DIR)
            ->willReturn($isCreated);
    }

    /**
     * @param string $dbCode
     * @param string $dbFileName
     */
    protected function configureGetDbFileNameMethod($dbCode, $dbFileName)
    {
        $this->database->expects($this->atLeastOnce())
            ->method('getDbFileName')
            ->with($dbCode)
            ->willReturn($dbFileName);
    }

    /**
     * @param string $dbCode
     * @param string $dbPath
     * @param string $dbArchPath
     * @param string $dbUrl
     * @param string $dbContent
     */
    protected function configureDownload($dbCode, $dbPath, $dbArchPath, $dbUrl, $dbContent)
    {
        $this->updater->expects($this->once())
            ->method('loadDbContent')
            ->with($dbUrl)
            ->willReturn($dbContent);

        $this->database->expects($this->atLeastOnce())
            ->method('getDbPath')
            ->with($dbCode)
            ->willReturn($dbPath);

        $this->directory->expects($this->atLeastOnce())
            ->method('writeFile')
            ->with($dbArchPath, $dbContent);
    }

    protected function configureUnpack($dbPath, $dbArchPath, $dbPathAbsolute, $dbArchPathAbsolute)
    {
        $this->directory->expects($this->exactly(2))
            ->method('getAbsolutePath')
            ->willReturnMap([
                [$dbPath, $dbPathAbsolute],
                [$dbArchPath, $dbArchPathAbsolute],
            ]);

        $this->archive->expects($this->once())
            ->method('unpack')
            ->with($dbArchPathAbsolute, $dbPathAbsolute);
    }
}

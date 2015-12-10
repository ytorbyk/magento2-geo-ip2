<?php
/**
 * Copyright © 2015 ToBai. All rights reserved.
 */

namespace Tobai\GeoIp2\Test\Unit\Model;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager as ObjectManagerHelper;
use Magento\Framework\App\Filesystem\DirectoryList;
use Tobai\GeoIp2\Model\Database;

/**
 * Class DatabaseTest
 */
class DatabaseTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Tobai\GeoIp2\Model\Database
     */
    protected $database;

    /**
     * @var \Magento\Framework\Filesystem\Directory\WriteInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $directory;

    /**
     * Test database list
     *
     * @var array
     */
    protected $databases = [
        'one_database' => 'one-database-name.mmd',
        'second_database' => 'second-database-name.mmd'
    ];

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

        $objectManagerHelper = new ObjectManagerHelper($this);
        $this->database = $objectManagerHelper->getObject(
            'Tobai\GeoIp2\Model\Database',
            [
                'filesystem' => $filesystem,
                'databases' => $this->databases
            ]
        );
    }

    public function testGetBasePath()
    {
        $basePath = 'some_path';
        $this->configureGetBasePathMethod($basePath);
        $this->assertEquals($basePath, $this->database->getBasePath());
    }

    /**
     * @param string $basePathMockValue
     */
    protected function configureGetBasePathMethod($basePathMockValue)
    {
        $this->directory->expects($this->once())
            ->method('getAbsolutePath')
            ->with(Database::BASE_DIR)
            ->willReturn($basePathMockValue);
    }

    /**
     * @param string $dbCode
     * @param string $dbName
     * @dataProvider getDbFileNameDataProvider
     */
    public function testGetDbFileName($dbCode, $dbName)
    {
        $this->assertEquals($dbName, $this->database->getDbFileName($dbCode));
    }

    /**
     * @return array
     */
    public function getDbFileNameDataProvider()
    {
        return [
            'one db' => ['dbCode' => 'one_database', 'dbName' => 'one-database-name.mmd'],
            'second db' => ['dbCode' => 'second_database', 'dbName' => 'second-database-name.mmd'],
            'non existing db' => ['dbCode' => 'non_existing', 'dbName' => false]
        ];
    }

    /**
     * @param string $dbCode
     * @param string $absolute
     * @param string $basePath
     * @param string $dbPath
     * @dataProvider getDbPathDataProvider
     */
    public function testGetDbPath($dbCode, $absolute, $basePath, $dbPath)
    {
        if ($absolute) {
            $this->configureGetBasePathMethod($basePath);
        }
        $this->assertEquals($dbPath, $this->database->getDbPath($dbCode, $absolute));
    }

    /**
     * @return array
     */
    public function getDbPathDataProvider()
    {
        return [
            'one db - non absolute' => [
                'dbCode' => 'one_database',
                'absolute' => false,
                'basePath' => '',
                'dbPath' => $this->getDbPathNonAbsolute('one_database')
            ],
            'one db - absolute' => [
                'dbCode' => 'one_database',
                'absolute' => true,
                'basePath' => 'some_path',
                'dbPath' => 'some_path' . DIRECTORY_SEPARATOR . 'one-database-name.mmd'
            ],
            'second db - non absolute' => [
                'dbCode' => 'second_database',
                'absolute' => false,
                'basePath' => '',
                'dbPath' => $this->getDbPathNonAbsolute('second_database')
            ],
            'second db - absolute' => [
                'dbCode' => 'second_database',
                'absolute' => true,
                'basePath' => 'some_path',
                'dbPath' => 'some_path' . DIRECTORY_SEPARATOR . 'second-database-name.mmd'
            ],
            'non existing db - non absolute' => [
                'dbCode' => 'non_existing',
                'absolute' => false,
                'basePath' => '',
                'dbPath' => $this->getDbPathNonAbsolute('non_existing')
            ],
            'non existing db - absolute' => [
                'dbCode' => 'non_existing',
                'absolute' => true,
                'basePath' => 'some_path',
                'dbPath' => false
            ]
        ];
    }

    protected function getDbPathNonAbsolute($dbCode)
    {
        return isset($this->databases[$dbCode])
            ? Database::BASE_DIR . DIRECTORY_SEPARATOR . $this->databases[$dbCode]
            : false;
    }

    /**
     * @param string $dbCode
     * @param bool $isFile
     * @param bool $isReadable
     * @param bool $isDbAvailable
     * @dataProvider isDbAvailableDataProvider
     */
    public function testIsDbAvailable($dbCode, $isFile, $isReadable, $isDbAvailable)
    {
        $this->configureDirectoryIsFileIsReadableMethods($dbCode, $isFile, $isReadable);
        $this->assertEquals($isDbAvailable, $this->database->isDbAvailable($dbCode));
    }

    protected function configureDirectoryIsFileIsReadableMethods($dbCode, $isFile, $isReadable)
    {
        $dbPath = $this->getDbPathNonAbsolute($dbCode);

        if ($dbPath) {
            $this->directory->expects($this->once())
                ->method('isFile')
                ->with($dbPath)
                ->willReturn($isFile);
        }

        if ($dbPath && $isFile) {
            $this->directory->expects($this->once())
                ->method('isReadable')
                ->with($dbPath)
                ->willReturn($isReadable);
        }
    }

    /**
     * @return array
     */
    public function isDbAvailableDataProvider()
    {
        return [
            'one db - file - readable' => [
                'dbCode' => 'one_database',
                'isFile' => true,
                'isReadable' => true,
                'isDbAvailable' => true
            ],
            'one db - non file - readable' => [
                'dbCode' => 'one_database',
                'isFile' => false,
                'isReadable' => true,
                'isDbAvailable' => false
            ],
            'one db - file - non readable' => [
                'dbCode' => 'one_database',
                'isFile' => true,
                'isReadable' => false,
                'isDbAvailable' => false
            ],
            'non existing db - non file - non readable' => [
                'dbCode' => 'non_existing',
                'isFile' => false,
                'isReadable' => false,
                'isDbAvailable' => false
            ]
        ];
    }

    /**
     * @param string $dbCode
     * @param array $stat
     * @param string|null $stateCode
     * @param string|array|bool $result
     * @dataProvider getDbFileStatDataProvider
     */
    public function testGetDbFileStat($dbCode, $stat, $stateCode, $result)
    {
        $this->configureDirectoryIsFileIsReadableMethods($dbCode, true, true);

        $dbPath = $this->getDbPathNonAbsolute($dbCode);
        if ($dbPath) {
            $this->directory->expects($this->once())
                ->method('stat')
                ->with($dbPath)
                ->willReturn($stat);
        }

        $this->assertEquals($result, $this->database->getDbFileStat($dbCode, $stateCode));
    }

    public function getDbFileStatDataProvider()
    {
        return [
            'existing db - all stat' => [
                'dbCode' => 'one_database',
                'stat' => ['name-1' => 'value-1', 'value-2' => 'value-2'],
                'stateCode' => null,
                'result' => ['name-1' => 'value-1', 'value-2' => 'value-2']
            ],
            'existing db - one stat value' => [
                'dbCode' => 'one_database',
                'stat' => ['name-1' => 'value-1', 'name-2' => 'value-2'],
                'stateCode' => 'name-2',
                'result' => 'value-2'
            ],
            'non existing db - all stat' => [
                'dbCode' => 'non_existing',
                'stat' => ['name-1' => 'value-1', 'value-2' => 'value-2'],
                'stateCode' => null,
                'result' => false
            ],
            'non existing db - one stat value' => [
                'dbCode' => 'non_existing',
                'stat' => ['name-1' => 'value-1', 'name-2' => 'value-2'],
                'stateCode' => 'name-1',
                'result' => false
            ]
        ];
    }
}

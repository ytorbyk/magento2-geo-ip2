<?php
/**
 * Copyright © 2015 ToBai. All rights reserved.
 */

namespace Tobai\GeoIp2\Test\Unit\Model\Database;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager as ObjectManagerHelper;

/**
 * Class ReaderFactoryTest
 */
class ReaderFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Tobai\GeoIp2\Model\Database\ReaderFactory
     */
    protected $readerFactory;

    /**
     * @var \Tobai\GeoIp2\Model\Database|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $database;

    /**
     * @var \Magento\Framework\ObjectManagerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $objectManager;

    /**
     * @var string
     */
    protected $instanceName = 'some class name';

    protected function setUp()
    {
        $this->database = $this->getMockBuilder('Tobai\GeoIp2\Model\Database')
            ->disableOriginalConstructor()
            ->getMock();

        $this->objectManager = $this->getMockBuilder('Magento\Framework\ObjectManagerInterface')
            ->disableOriginalConstructor()
            ->getMock();

        $objectManagerHelper = new ObjectManagerHelper($this);
        $this->readerFactory = $objectManagerHelper->getObject(
            'Tobai\GeoIp2\Model\Database\ReaderFactory',
            [
                'database' => $this->database,
                'objectManager' => $this->objectManager,
                'instanceName' => $this->instanceName
            ]
        );
    }

    public function testCreate()
    {
        $dbCode = 'some_db_code';
        $locales = ['en', 'fr'];
        $dbPath = 'some_absolute_db_path';
        $data = ['filename' => $dbPath, 'locales' => ['en', 'fr']];

        $this->database->expects($this->once())
            ->method('isDbAvailable')
            ->with($dbCode)
            ->willReturn(true);

        $this->database->expects($this->once())
            ->method('getDbPath')
            ->with($dbCode, true)
            ->willReturn($dbPath);

        $reader = $this->getMockBuilder('GeoIp2\Database\Reader')
            ->disableOriginalConstructor()
            ->getMock();

        $this->objectManager->expects($this->once())
            ->method('create')
            ->with($this->instanceName, $data)
            ->willReturn($reader);

        $this->assertSame($reader, $this->readerFactory->create($dbCode, $locales));
    }

    /**
     * @expectedException \Magento\Framework\Exception\LocalizedException
     * @expectedExceptionMessage GeoIp2 database with "some_db_code" code is not declared.
     */
    public function testCreateWithNoAvailableDb()
    {
        $dbCode = 'some_db_code';

        $this->database->expects($this->once())
            ->method('isDbAvailable')
            ->with($dbCode)
            ->willReturn(false);

        $this->database->expects($this->never())
            ->method('getDbPath');

        $this->objectManager->expects($this->never())
            ->method('create');

        $this->readerFactory->create($dbCode);
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessageRegExp %s must be an instance of \GeoIp2\Database\Reader.
     */
    public function testCreateWithWrongReaderClass()
    {
        $dbCode = 'some_db_code';
        $dbPath = 'some_absolute_db_path';
        $data = ['filename' => $dbPath, 'locales' => ['en']];

        $this->database->expects($this->once())
            ->method('isDbAvailable')
            ->with($dbCode)
            ->willReturn(true);

        $this->database->expects($this->once())
            ->method('getDbPath')
            ->with($dbCode, true)
            ->willReturn($dbPath);

        $reader = $this->getMockBuilder('GeoIp2\Database\WrongReader')
            ->disableOriginalConstructor()
            ->getMock();

        $this->objectManager->expects($this->once())
            ->method('create')
            ->with($this->instanceName, $data)
            ->willReturn($reader);

        $this->readerFactory->create($dbCode);
    }
}

<?php
/**
 * Copyright © 2015 ToBai. All rights reserved.
 */

namespace Tobai\GeoIp2\Test\Unit\Cron;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager as ObjectManagerHelper;

/**
 * Class ObserverTest
 */
class UpdateDbTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Tobai\GeoIp2\Cron\UpdateDb
     */
    protected $cronUpdateDb;

    /**
     * @var \Tobai\GeoIp2\Model\Database\Updater\Selected|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $updaterSelected;

    /**
     * @var \Tobai\GeoIp2\Model\Database\Config|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $databaseConfig;

    /**
     * @var \Psr\Log\LoggerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $logger;

    protected function setUp()
    {
        $this->updaterSelected = $this->getMockBuilder('Tobai\GeoIp2\Model\Database\Updater\Selected')
            ->disableOriginalConstructor()
            ->getMock();

        $this->databaseConfig = $this->getMockBuilder('Tobai\GeoIp2\Model\Database\Config')
            ->disableOriginalConstructor()
            ->getMock();

        $this->logger = $this->getMock('Psr\Log\LoggerInterface');

        $objectManagerHelper = new ObjectManagerHelper($this);
        $this->cronUpdateDb = $objectManagerHelper->getObject(
            'Tobai\GeoIp2\Cron\UpdateDb',
            [
                'updaterSelected' => $this->updaterSelected,
                'databaseConfig' => $this->databaseConfig,
                'logger' => $this->logger
            ]
        );
    }

    /**
     * @param bool $isAutoUpdate
     * @param int $updateMethodCall
     * @dataProvider updateDbDataProvider
     */
    public function testUpdateDb($isAutoUpdate, $updateMethodCall)
    {
        $this->databaseConfig->expects($this->once())
            ->method('isAutoUpdate')
            ->willReturn($isAutoUpdate);

        $this->updaterSelected->expects($this->exactly($updateMethodCall))
            ->method('update');

        $this->logger->expects($this->never())
            ->method('critical');

        $this->assertSame($this->cronUpdateDb, $this->cronUpdateDb->execute());
    }

    /**
     * @return array
     */
    public function updateDbDataProvider()
    {
        return [
            'enabled' => ['isAutoUpdate' => true, 'updateMethodCall' => 1],
            'disabled' => ['isAutoUpdate' => false, 'updateMethodCall' => 0]
        ];
    }

    public function testUpdateDbWithException()
    {
        $this->databaseConfig->expects($this->once())
            ->method('isAutoUpdate')
            ->willReturn(true);

        $e = new \Exception;
        $this->updaterSelected->expects($this->once())
            ->method('update')
            ->willThrowException($e);

        $this->logger->expects($this->once())
            ->method('critical')
            ->with($e);

        $this->assertSame($this->cronUpdateDb, $this->cronUpdateDb->execute());
    }
}

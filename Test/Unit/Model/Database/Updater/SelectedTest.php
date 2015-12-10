<?php
/**
 * Copyright © 2015 ToBai. All rights reserved.
 */

namespace Tobai\GeoIp2\Test\Unit\Model\Database\Updater;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager as ObjectManagerHelper;

/**
 * Class SelectedTest
 */
class SelectedTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Tobai\GeoIp2\Model\Database\Updater\Selected
     */
    protected $updaterSelected;

    /**
     * @var \Tobai\GeoIp2\Model\Database\Config|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $config;

    /**
     * @var \Tobai\GeoIp2\Model\Database\UpdaterInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $updater;

    public function setUp()
    {
        $this->config = $this->getMockBuilder('Tobai\GeoIp2\Model\Database\Config')
            ->disableOriginalConstructor()
            ->getMock();

        $this->updater = $this->getMock('Tobai\GeoIp2\Model\Database\UpdaterInterface');

        $objectManagerHelper = new ObjectManagerHelper($this);
        $this->updaterSelected = $objectManagerHelper->getObject(
            'Tobai\GeoIp2\Model\Database\Updater\Selected',
            [
                'config' => $this->config,
                'updater' => $this->updater
            ]
        );
    }

    public function testUpdate()
    {
        $availableDatabases = ['db_code_1', 'db_code_2', 'db_cod_3'];
        $countAvailableDatabases = count($availableDatabases);

        $this->config->expects($this->once())
            ->method('getAvailableDatabases')
            ->willReturn($availableDatabases);

        $this->updater->expects($this->exactly($countAvailableDatabases))
            ->method('update')
            ->withConsecutive(['db_code_1'], ['db_code_2'], ['db_cod_3']);

        $this->updaterSelected->update();
    }
}

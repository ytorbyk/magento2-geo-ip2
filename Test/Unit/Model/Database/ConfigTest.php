<?php
/**
 * Copyright © 2015 ToBai. All rights reserved.
 */

namespace Tobai\GeoIp2\Test\Unit\Model\Database;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager as ObjectManagerHelper;

/**
 * Class ConfigTest
 */
class ConfigTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Tobai\GeoIp2\Model\Database\Config
     */
    protected $config;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $scopeConfig;

    protected function setUp()
    {
        $this->scopeConfig = $this->getMock('Magento\Framework\App\Config\ScopeConfigInterface');

        $objectManagerHelper = new ObjectManagerHelper($this);
        $this->config = $objectManagerHelper->getObject(
            'Tobai\GeoIp2\Model\Database\Config',
            ['scopeConfig' => $this->scopeConfig]
        );
    }

    /**
     * @param string $availableDatabases
     * @param array $expectedValue
     * @dataProvider getAvailableDatabasesDataProvider
     */
    public function testGetAvailableDatabases($availableDatabases, $expectedValue)
    {
        $this->scopeConfig->expects($this->once())
            ->method('getValue')
            ->with('tobai_geoip2/database/available')
            ->willReturn($availableDatabases);

        $this->assertEquals($expectedValue, $this->config->getAvailableDatabases());
    }

    /**
     * @return array
     */
    public function getAvailableDatabasesDataProvider()
    {
        return [
            'one db code' => [
                'availableDatabases' => 'dbCode',
                'expectedValue' => ['dbCode']
            ],
            'several db codes' => [
                'availableDatabases' => 'dbCode1,dbCode2,dbCode3',
                'expectedValue' => ['dbCode1', 'dbCode2', 'dbCode3']
            ],
            'empty' => [
                'availableDatabases' => '',
                'expectedValue' => []
            ]
        ];
    }

    public function testIsAutoUpdate()
    {
        $isAutoUpdate = true;

        $this->scopeConfig->expects($this->once())
            ->method('isSetFlag')
            ->with('tobai_geoip2/database/auto_update')
            ->willReturn($isAutoUpdate);

        $this->assertEquals($isAutoUpdate, $this->config->isAutoUpdate());
    }
}

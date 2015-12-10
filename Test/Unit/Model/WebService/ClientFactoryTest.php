<?php
/**
 * Copyright © 2015 ToBai. All rights reserved.
 */

namespace Tobai\GeoIp2\Test\Unit\Model\WebService;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager as ObjectManagerHelper;

/**
 * Class ClientFactoryTest
 */
class ClientFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Tobai\GeoIp2\Model\WebService\ClientFactory
     */
    protected $clientFactory;

    /**
     * @var \Tobai\GeoIp2\Model\WebService\Config|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $config;

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
        $this->config = $this->getMockBuilder('Tobai\GeoIp2\Model\WebService\Config')
            ->disableOriginalConstructor()
            ->getMock();

        $this->objectManager = $this->getMockBuilder('Magento\Framework\ObjectManagerInterface')
            ->disableOriginalConstructor()
            ->getMock();

        $objectManagerHelper = new ObjectManagerHelper($this);
        $this->clientFactory = $objectManagerHelper->getObject(
            'Tobai\GeoIp2\Model\WebService\ClientFactory',
            [
                'config' => $this->config,
                'objectManager' => $this->objectManager,
                'instanceName' => $this->instanceName
            ]
        );
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessageRegExp %s must be an instance of \GeoIp2\WebService\Client.
     */
    public function testCreateWithWrongClientClass()
    {
        $options = [];
        $data = [
            'userId' => 'config user id',
            'licenseKey' => 'config license key',
            'options' => ['host' => 'config host']
        ];

        $this->configureConfigMethods('config user id', 'config license key', 'config host');

        $client = $this->getMockBuilder('GeoIp2\WebService\WrongClient')
            ->disableOriginalConstructor()
            ->getMock();

        $this->objectManager->expects($this->once())
            ->method('create')
            ->with($this->instanceName, $data)
            ->willReturn($client);

        $this->clientFactory->create($options);
    }

    /**
     * @param array|string $options
     * @param array $data
     * @dataProvider createDataProvider
     */
    public function testCreate($options, $data)
    {
        $this->configureConfigMethods('config user id', 'config license key', 'config host');

        $client = $this->getMockBuilder('GeoIp2\WebService\Client')
            ->disableOriginalConstructor()
            ->getMock();

        $this->objectManager->expects($this->once())
            ->method('create')
            ->with($this->instanceName, $data)
            ->willReturn($client);

        $this->assertSame($client, $this->clientFactory->create($options));
    }

    public function createDataProvider()
    {
        return [
            'nothing passed' => [
                'options' => [],
                'data' => [
                    'userId' => 'config user id',
                    'licenseKey' => 'config license key',
                    'options' => ['host' => 'config host']
                ]
            ],
            'user id and host passed' => [
                'options' => [
                    'userId' => 'passed user id',
                    'options' => ['host' => 'passed host']
                ],
                'data' => [
                    'userId' => 'passed user id',
                    'licenseKey' => 'config license key',
                    'options' => ['host' => 'passed host']
                ]
            ],
            'user id, license key and host passed' => [
                'options' => [
                    'userId' => 'passed user id',
                    'licenseKey' => 'passed license key',
                    'options' => ['host' => 'passed host', 'some_key' => 'some_value']
                ],
                'data' => [
                    'userId' => 'passed user id',
                    'licenseKey' => 'passed license key',
                    'options' => ['host' => 'passed host', 'some_key' => 'some_value']
                ]
            ],
            'license key and host passed. option as string' => [
                'options' => [
                    'licenseKey' => 'passed license key',
                    'options' => 'passed host'
                ],
                'data' => [
                    'userId' => 'config user id',
                    'licenseKey' => 'passed license key',
                    'options' => ['host' => 'passed host']
                ]
            ]
        ];
    }

    protected function configureConfigMethods($userId, $licenseKey, $host)
    {
        $this->config->expects($this->once())
            ->method('getUserId')
            ->willReturn($userId);

        $this->config->expects($this->once())
            ->method('getLicenseKey')
            ->willReturn($licenseKey);

        $this->config->expects($this->once())
            ->method('getHost')
            ->willReturn($host);
    }
}

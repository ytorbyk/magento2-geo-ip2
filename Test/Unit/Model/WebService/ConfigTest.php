<?php
/**
 * Copyright © 2015 ToBai. All rights reserved.
 */

namespace Tobai\GeoIp2\Test\Unit\Model\WebService;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager as ObjectManagerHelper;

/**
 * Class ConfigTest
 */
class ConfigTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Tobai\GeoIp2\Model\WebService\Config
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
            'Tobai\GeoIp2\Model\WebService\Config',
            ['scopeConfig' => $this->scopeConfig]
        );
    }

    protected function configureGetValueMethod($path, $value)
    {
        $this->scopeConfig->expects($this->once())
            ->method('getValue')
            ->with($path)
            ->willReturn($value);
    }

    public function testGetUserId()
    {
        $userId = 'some-user-id';
        $this->configureGetValueMethod('tobai_geoip2/web_service/user_id', $userId);
        $this->assertEquals($userId, $this->config->getUserId());
    }

    public function testGetLicenseKey()
    {
        $licenseKey = 'some-license_key';
        $this->configureGetValueMethod('tobai_geoip2/web_service/license_key', $licenseKey);
        $this->assertEquals($licenseKey, $this->config->getLicenseKey());
    }

    public function testGetHost()
    {
        $host = 'some-host';
        $this->configureGetValueMethod('tobai_geoip2/web_service/host', $host);
        $this->assertEquals($host, $this->config->getHost());
    }
}

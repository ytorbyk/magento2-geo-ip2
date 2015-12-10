<?php
/**
 * Copyright © 2015 ToBai. All rights reserved.
 */

namespace Tobai\GeoIp2\Test\Unit\Model\Database\Data;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager as ObjectManagerHelper;

/**
 * Class CityTest
 */
class CityTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Tobai\GeoIp2\Model\Database\Data\City
     */
    protected $city;

    /**
     * @var \Tobai\GeoIp2\Model\Database\ReaderFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $readerFactory;

    /**
     * @var \Magento\Framework\HTTP\PhpEnvironment\Request|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $httpRequest;

    /**
     * @var \Psr\Log\LoggerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $logger;

    protected function setUp()
    {
        $this->readerFactory = $this->getMockBuilder('Tobai\GeoIp2\Model\Database\ReaderFactory')
            ->disableOriginalConstructor()
            ->getMock();

        $this->httpRequest = $this->getMockBuilder('Magento\Framework\HTTP\PhpEnvironment\Request')
            ->disableOriginalConstructor()
            ->getMock();

        $this->logger = $this->getMock('Psr\Log\LoggerInterface');

        $objectManagerHelper = new ObjectManagerHelper($this);
        $this->city = $objectManagerHelper->getObject(
            'Tobai\GeoIp2\Model\Database\Data\City',
            [
                'readerFactory' => $this->readerFactory,
                'httpRequest' => $this->httpRequest,
                'logger' => $this->logger
            ]
        );
    }

    protected function configureHttpRequest($ip)
    {
        $this->httpRequest->expects($this->once())
            ->method('getClientIp')
            ->willReturn($ip);
    }

    /**
     * @return \GeoIp2\Database\Reader|\PHPUnit_Framework_MockObject_MockObject
     */
    protected function configureReaderFactory()
    {
        $reader = $this->getMockBuilder('GeoIp2\Database\Reader')
            ->disableOriginalConstructor()
            ->getMock();

        $this->readerFactory->expects($this->once())
            ->method('create')
            ->willReturn($reader);

        return $reader;
    }

    public function testGetCity()
    {
        $ip  = 'some ip';

        $this->configureHttpRequest($ip);

        $reader = $this->configureReaderFactory();

        $geoCity = $this->getMockBuilder('GeoIp2\Model\City')
            ->disableOriginalConstructor()
            ->getMock();

        $reader->expects($this->once())
            ->method('city')
            ->with($ip)
            ->willReturn($geoCity);

        $this->logger->expects($this->never())
            ->method('critical');

        $this->assertSame($geoCity, $this->city->getCity());
    }

    public function testGetCityWithException()
    {
        $ip  = 'some ip';
        $e = new \Exception();

        $this->configureHttpRequest($ip);

        $reader = $this->configureReaderFactory();

        $reader->expects($this->once())
            ->method('city')
            ->willThrowException($e);

        $this->logger->expects($this->once())
            ->method('critical')
            ->with($e);

        $this->assertFalse($this->city->getCity());
    }

    public function testGetCountryCode()
    {
        $ip  = 'some ip';
        $countryCode = 'some country code';

        $this->configureHttpRequest($ip);

        $reader = $this->configureReaderFactory();

        $geoCity = $this->configureGeoCityObject($countryCode);

        $reader->expects($this->once())
            ->method('city')
            ->with($ip)
            ->willReturn($geoCity);

        $this->logger->expects($this->never())
            ->method('critical');

        $this->assertSame($countryCode, $this->city->getCountryCode());
    }

    protected function configureGeoCityObject($countryCode)
    {
        $recordCountry = $this->getMockBuilder('GeoIp2\Record\Country')
            ->disableOriginalConstructor()
            ->setMethods(null)
            ->getMock();
        $recordCountry->isoCode = $countryCode;

        $geoCity = $this->getMockBuilder('GeoIp2\Model\City')
            ->disableOriginalConstructor()
            ->setMethods(null)
            ->getMock();

        $reflection = new \ReflectionClass('GeoIp2\Model\City');
        $reflectionProperty = $reflection->getProperty('country');
        $reflectionProperty->setAccessible(true);
        $reflectionProperty->setValue($geoCity, $recordCountry);

        return $geoCity;
    }

    public function testGetCountryCodeWithException()
    {
        $ip  = 'some ip';
        $e = new \Exception();

        $this->configureHttpRequest($ip);

        $reader = $this->configureReaderFactory();

        $reader->expects($this->once())
            ->method('city')
            ->with($ip)
            ->willThrowException($e);

        $this->logger->expects($this->once())
            ->method('critical')
            ->with($e);

        $this->assertFalse($this->city->getCountryCode());
    }
}

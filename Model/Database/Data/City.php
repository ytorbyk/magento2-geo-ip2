<?php
/**
 * Copyright © 2015 ToBai. All rights reserved.
 */

namespace Tobai\GeoIp2\Model\Database\Data;

use Tobai\GeoIp2\Model\CountryInterface;
use Tobai\GeoIp2\Model\Database\ReaderFactory;
use Magento\Framework\HTTP\PhpEnvironment\Request;
use Psr\Log\LoggerInterface as Logger;

class City implements CountryInterface
{
    /**
     * @var \Tobai\GeoIp2\Model\Database\ReaderFactory
     */
    protected $readerFactory;

    /**
     * @var \GeoIp2\Database\Reader
     */
    protected $reader;

    /**
     * @var \Magento\Framework\HTTP\PhpEnvironment\Request
     */
    protected $httpRequest;

    /**
     * @var Logger
     */
    protected $logger;

    /**
     * @param \Tobai\GeoIp2\Model\Database\ReaderFactory $readerFactory
     * @param \Magento\Framework\HTTP\PhpEnvironment\Request $httpRequest
     * @param Logger $logger
     */
    public function __construct(
        ReaderFactory $readerFactory,
        Request $httpRequest,
        Logger $logger
    ) {
        $this->readerFactory = $readerFactory;
        $this->httpRequest = $httpRequest;
        $this->logger = $logger;
    }

    /**
     * @return \GeoIp2\Model\City|bool
     */
    public function getCity()
    {
        try {
            $ip = $this->httpRequest->getClientIp();
            $city = $this->getReader()->city($ip);
        } catch (\Exception $e) {
            $this->logger->critical($e);
            $city = false;
        }
        return $city;
    }

    /**
     * @return bool|string
     */
    public function getCountryCode()
    {
        $city = $this->getCity();
        return $city ? $city->country->isoCode : false;
    }

    /**
     * @return \GeoIp2\Database\Reader
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function getReader()
    {
        if (null === $this->reader) {
            $this->reader = $this->readerFactory->create('city');
        }
        return $this->reader;
    }
}

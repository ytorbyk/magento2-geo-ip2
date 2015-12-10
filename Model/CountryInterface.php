<?php
/**
 * Copyright © 2015 ToBai. All rights reserved.
 */

namespace Tobai\GeoIp2\Model;

/**
 * Interface CountryInterface
 */
interface CountryInterface
{
    /**
     * @return string|false
     */
    public function getCountryCode();
}

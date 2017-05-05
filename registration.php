<?php
/**
 * Copyright © 2015 ToBai. All rights reserved.
 */

\Magento\Framework\Component\ComponentRegistrar::register(
    \Magento\Framework\Component\ComponentRegistrar::MODULE,
    'Tobai_GeoIp2',
    __DIR__
);

// Register geoip2/geoip2 compoer dependency as Magento lib
\Magento\Framework\Component\ComponentRegistrar::register(
    \Magento\Framework\Component\ComponentRegistrar::LIBRARY,
    'Tobai_GeoIp2_Lib',
    __DIR__ . '\..\..\geoip2\geoip2'
);

<?php
/**
 * Copyright © 2015 ToBai. All rights reserved.
 */

namespace Tobai\GeoIp2\Model\Database;

use Magento\Framework\Exception\LocalizedException;

interface UpdaterInterface
{
    /**
     * @param string $dbCode
     * @throws LocalizedException
     */
    public function update($dbCode);
}

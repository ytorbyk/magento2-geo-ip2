<?php
/**
 * Copyright © 2015 ToBai. All rights reserved.
 */

namespace Tobai\GeoIp2\Model\System\Config\Source;

/**
 * Used in creating options for ChooseDb config value selection
 */
class AvailableDb implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @var array
     */
    protected $databases = [];

    /**
     * @param array $databases
     */
    public function __construct(array $databases = [])
    {
        $this->databases = $databases;
    }

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        $option = [];
        foreach ($this->databases as $dbCode => $dbTitle) {
            $option[] = ['value' => $dbCode, 'label' => $dbTitle];
        }
        return $option;
    }

    /**
     * @param string|null $dbCode
     * @return array|string
     */
    public function getOptionTitle($dbCode = null)
    {
        return null === $dbCode
            ? $this->databases
            : (isset($this->databases[$dbCode]) ? $this->databases[$dbCode] : '');
    }
}

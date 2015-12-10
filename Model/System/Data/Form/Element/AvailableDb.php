<?php
/**
 * Copyright © 2015 ToBai. All rights reserved.
 */

namespace Tobai\GeoIp2\Model\System\Data\Form\Element;

use Magento\Framework\Data\Form\Element;
use Magento\Framework\Escaper;
use Tobai\GeoIp2\Model\System\Config\Source;

/**
 * Class AvailableDb
 */
class AvailableDb extends Element\Multiselect
{
    /**
     * @var \Tobai\GeoIp2\Model\System\Config\Source\AvailableDb
     */
    protected $sourceAvailableDb;

    /**
     * @param \Tobai\GeoIp2\Model\System\Config\Source\AvailableDb $sourceAvailableDb
     * @param \Magento\Framework\Data\Form\Element\Factory $factoryElement
     * @param \Magento\Framework\Data\Form\Element\CollectionFactory $factoryCollection
     * @param \Magento\Framework\Escaper $escaper
     * @param array $data
     */
    public function __construct(
        Source\AvailableDb $sourceAvailableDb,
        Element\Factory $factoryElement,
        Element\CollectionFactory $factoryCollection,
        Escaper $escaper,
        $data = []
    ) {
        $this->sourceAvailableDb = $sourceAvailableDb;
        parent::__construct($factoryElement, $factoryCollection, $escaper, $data);
    }

    /**
     * @return string
     */
    public function getElementHtml()
    {
        $this->setSize($this->calculateSize());
        return parent::getElementHtml();
    }

    /**
     * @return int
     */
    protected function calculateSize()
    {
        $size = count($this->sourceAvailableDb->toOptionArray());
        return $size > 6 ? 6 : $size;
    }
}

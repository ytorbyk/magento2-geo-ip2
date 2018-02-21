<?php
/**
 * Copyright © 2015 ToBai. All rights reserved.
 */

namespace Tobai\GeoIp2\Block\Adminhtml\System\Config;

use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Config\Block\System\Config\Form\Field;

/**
 * Class Status
 */
class Status extends Field
{
    /**
     * @var \Tobai\GeoIp2\Model\Database\Config
     */
    protected $config;

    /**
     * @var \Tobai\GeoIp2\Model\Database
     */
    protected $database;

    /**
     * @var \Tobai\GeoIp2\Model\System\Config\Source\AvailableDb
     */
    protected $availableDb;

    /**
     * @param \Tobai\GeoIp2\Model\Database\Config $config
     * @param \Tobai\GeoIp2\Model\Database $database
     * @param \Tobai\GeoIp2\Model\System\Config\Source\AvailableDb $availableDb
     * @param \Magento\Backend\Block\Template\Context $context
     * @param array $data
     */
    public function __construct(
        \Tobai\GeoIp2\Model\Database\Config $config,
        \Tobai\GeoIp2\Model\Database $database,
        \Tobai\GeoIp2\Model\System\Config\Source\AvailableDb $availableDb,
        \Magento\Backend\Block\Template\Context $context,
        array $data = []
    ) {
        $this->config = $config;
        $this->database = $database;
        $this->availableDb = $availableDb;
        parent::__construct($context, $data);
    }

    /**
     * @param AbstractElement $element
     * @return string
     */
    protected function _getElementHtml(AbstractElement $element)
    {
        $element->unsScope();
        $element->unsCanUseWebsiteValue();
        $element->unsCanUseDefaultValue();
        return $this->getDbStatus();
    }

    /**
     * @return string
     */
    public function getDbStatus()
    {
        $dbCodes = $this->config->getAvailableDatabases();
        $html = '<ul style="list-style: none; margin: 0;">';
        foreach ($dbCodes as $dbCode) {
            $html .= "<li>{$this->availableDb->getOptionTitle($dbCode)}: {$this->getDbCreateDate($dbCode)}</li>";
        }
        $html .= '</ul>';
        return $html;
    }

    /**
     * @param string $dbCode
     * @return \Magento\Framework\Phrase|string
     */
    protected function getDbCreateDate($dbCode)
    {
        return $this->database->isDbAvailable($dbCode)
            ? $this->_localeDate->formatDate($this->getDbDateTime($dbCode), \IntlDateFormatter::MEDIUM)
            : __('Never');
    }

    /**
     * @param string $dbCode
     * @return \DateTime
     */
    protected function getDbDateTime($dbCode)
    {
        $dateTime = new \DateTime();
        $dateTime->setTimestamp($this->database->getDbFileStat($dbCode, 'mtime'));
        return $dateTime;
    }
}

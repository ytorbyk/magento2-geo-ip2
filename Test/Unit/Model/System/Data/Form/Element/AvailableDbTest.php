<?php
/**
 * Copyright © 2015 ToBai. All rights reserved.
 */

namespace Tobai\GeoIp2\Test\Unit\Model\System\Data\Form\Element;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager as ObjectManagerHelper;

/**
 * Class AvailableDbTest
 */
class AvailableDbTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Tobai\GeoIp2\Model\System\Data\Form\Element\AvailableDb
     */
    protected $availableDb;

    /**
     * @var \Tobai\GeoIp2\Model\System\Config\Source\AvailableDb|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $sourceAvailableDb;

    protected function setUp()
    {
        $this->sourceAvailableDb = $this->getMockBuilder('Tobai\GeoIp2\Model\System\Config\Source\AvailableDb')
            ->disableOriginalConstructor()
            ->getMock();

        $objectManagerHelper = new ObjectManagerHelper($this);
        $this->availableDb = $objectManagerHelper->getObject(
            'Tobai\GeoIp2\Model\System\Data\Form\Element\AvailableDb',
            ['sourceAvailableDb' => $this->sourceAvailableDb]
        );
    }

    /**
     * @param array $optionsAvailableDb
     * @param int $expectedSize
     * @dataProvider getElementHtmlDataProvider
     */
    public function testGetElementHtml($optionsAvailableDb, $expectedSize)
    {
        $this->sourceAvailableDb->expects($this->once())
            ->method('toOptionArray')
            ->willReturn($optionsAvailableDb);

        $form = $this->getMockBuilder('Magento\Framework\Data\Form')
            ->disableOriginalConstructor()
            ->getMock();
        $this->availableDb->setForm($form);

        $this->assertEquals(10, $this->availableDb->getData('size'));
        $this->availableDb->getElementHtml();
        $this->assertEquals($expectedSize, $this->availableDb->getData('size'));
    }

    /**
     * @return array
     */
    public function getElementHtmlDataProvider()
    {
        return [
            'less than 6 items' => [
                'optionsAvailableDb' => ['item1', 'item2', 'item3'],
                'expectedSize' => 3
            ],
            'more than 6 items' => [
                'optionsAvailableDb' => ['item1', 'item2', 'item3', 'item4', 'item5', 'item6', 'item7', 'item8'],
                'expectedSize' => 6
            ]
        ];
    }
}

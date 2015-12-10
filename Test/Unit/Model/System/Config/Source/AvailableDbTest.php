<?php
/**
 * Copyright © 2015 ToBai. All rights reserved.
 */

namespace Tobai\GeoIp2\Test\Unit\Model\System\Config\Source;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager as ObjectManagerHelper;

/**
 * Class AvailableDbTest
 */
class AvailableDbTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Tobai\GeoIp2\Model\System\Config\Source\AvailableDb
     */
    protected $availableDb;

    protected $databases = [
        'db_code_1' => 'db_title_1',
        'db_code_2' => 'db_title_2',
        'db_code_3' => 'db_title_3',
    ];

    protected function setUp()
    {
        $objectManagerHelper = new ObjectManagerHelper($this);
        $this->availableDb = $objectManagerHelper->getObject(
            'Tobai\GeoIp2\Model\System\Config\Source\AvailableDb',
            ['databases' => $this->databases]
        );
    }

    public function testToOptionArray()
    {
        $expectedOptionArray = [
            ['label' => 'db_title_1', 'value' => 'db_code_1'],
            ['label' => 'db_title_2', 'value' => 'db_code_2'],
            ['label' => 'db_title_3', 'value' => 'db_code_3'],
        ];
        $this->assertEquals($expectedOptionArray, $this->availableDb->toOptionArray());
    }

    /**
     * @param string|null $dbCode
     * @param string|array $expectedValue
     * @dataProvider getOptionTitleDataProvider
     */
    public function testGetOptionTitle($dbCode, $expectedValue)
    {
        $this->assertEquals($expectedValue, $this->availableDb->getOptionTitle($dbCode));
    }

    /**
     * @return array
     */
    public function getOptionTitleDataProvider()
    {
        return [
            'without code' => [
                'dbCode' => null,
                'expectedValue' => $this->databases
            ],
            'with db code' => [
                'dbCode' => 'db_code_2',
                'expectedValue' => 'db_title_2'
            ],
            'with non declared db code' => [
                'dbCode' => 'non_declared_db',
                'expectedValue' => ''
            ],
        ];
    }
}

<?php
/**
 * Copyright © 2015 ToBai. All rights reserved.
 */

namespace Tobai\GeoIp2\Test\Unit\Controller\Adminhtml\Database;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager as ObjectManagerHelper;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class UpdateTest
 */
class UpdateTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Tobai\GeoIp2\Controller\Adminhtml\Database\Update
     */
    protected $controllerUpdate;

    /**
     * @var \Tobai\GeoIp2\Model\Database\Updater\Selected|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $updaterSelected;

    /**
     * @var \Magento\Framework\View\LayoutFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $layoutFactory;

    /**
     * @var \Magento\Framework\Controller\Result\Json|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $resultJson;

    /**
     * @var \Psr\Log\LoggerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $logger;

    protected function setUp()
    {
        $this->updaterSelected = $this->getMockBuilder('Tobai\GeoIp2\Model\Database\Updater\Selected')
            ->disableOriginalConstructor()
            ->getMock();

        $this->resultJson = $this->getMockBuilder('Magento\Framework\Controller\Result\Json')
            ->disableOriginalConstructor()
            ->getMock();
        $resultFactory = $this->getMockBuilder('Magento\Framework\Controller\ResultFactory')
            ->disableOriginalConstructor()
            ->getMock();
        $resultFactory->expects($this->once())
            ->method('create')
            ->with(ResultFactory::TYPE_JSON)
            ->willReturn($this->resultJson);

        $this->layoutFactory = $this->getMockBuilder('Magento\Framework\View\LayoutFactory')
            ->disableOriginalConstructor()
            ->getMock();

        $this->logger = $this->getMock('Psr\Log\LoggerInterface');

        $objectManagerHelper = new ObjectManagerHelper($this);
        $this->controllerUpdate = $objectManagerHelper->getObject(
            'Tobai\GeoIp2\Controller\Adminhtml\Database\Update',
            [
                'updaterSelected' => $this->updaterSelected,
                'layoutFactory' => $this->layoutFactory,
                'resultFactory' => $resultFactory,
                'logger' => $this->logger
            ]
        );
    }

    public function testExecuteWithException()
    {
        $e = new \Exception();
        $data = [
            'status' => 'error',
            'message' => __('An error occurred during DB updating.')
        ];

        $this->resultJson->expects($this->once())
            ->method('setData')
            ->with($data)
            ->willReturnSelf();

        $this->updaterSelected->expects($this->once())
            ->method('update')
            ->willThrowException($e);

        $this->logger->expects($this->once())
            ->method('critical')
            ->with($e);

        $this->assertSame($this->resultJson, $this->controllerUpdate->execute());
    }

    public function testExecuteWithLocalizedException()
    {
        $textException = __('Text exection');
        $e = new LocalizedException($textException);
        $data = [
            'status' => 'error',
            'message' => $textException
        ];

        $this->resultJson->expects($this->once())
            ->method('setData')
            ->with($data)
            ->willReturnSelf();

        $this->updaterSelected->expects($this->once())
            ->method('update')
            ->willThrowException($e);

        $this->logger->expects($this->never())
            ->method('critical');

        $this->assertSame($this->resultJson, $this->controllerUpdate->execute());
    }

    public function testUpdate()
    {
        $statusInfo = 'some status info';
        $data = [
            'status' => 'success',
            'message' => __('Database(s) successfully updated.'),
            'status_info' => $statusInfo
        ];

        $this->resultJson->expects($this->once())
            ->method('setData')
            ->with($data)
            ->willReturnSelf();

        $this->updaterSelected->expects($this->once())
            ->method('update')
            ->willReturnSelf();

        $statusBlock = $this->getMockBuilder('Tobai\GeoIp2\Block\Adminhtml\System\Config\Status')
            ->disableOriginalConstructor()
            ->getMock();

        $layout = $this->getMock('Magento\Framework\View\LayoutInterface');

        $this->layoutFactory->expects($this->once())
            ->method('create')
            ->willReturn($layout);

        $layout->expects($this->once())
            ->method('createBlock')
            ->with('Tobai\GeoIp2\Block\Adminhtml\System\Config\Status')
            ->willReturn($statusBlock);

        $statusBlock->expects($this->once())
            ->method('getDbStatus')
            ->willReturn($statusInfo);

        $this->logger->expects($this->never())
            ->method('critical');

        $this->assertSame($this->resultJson, $this->controllerUpdate->execute());
    }
}

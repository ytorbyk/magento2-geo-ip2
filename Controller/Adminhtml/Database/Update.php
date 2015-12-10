<?php
/**
 * Copyright © 2015 ToBai. All rights reserved.
 */

namespace Tobai\GeoIp2\Controller\Adminhtml\Database;

use Tobai\GeoIp2\Model\Database\Updater\Selected;
use Magento\Backend\App\Action;
use Magento\Framework\View\LayoutFactory;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Exception\LocalizedException;
use Psr\Log\LoggerInterface as Logger;

class Update extends Action
{
    /**
     * @var \Tobai\GeoIp2\Model\Database\Updater\Selected
     */
    protected $updaterSelected;

    /**
     * @var \Magento\Framework\View\LayoutFactory
     */
    protected $layoutFactory;

    /**
     * @var Logger
     */
    protected $logger;

    /**
     * @param \Tobai\GeoIp2\Model\Database\Updater\Selected $updaterSelected
     * @param \Magento\Framework\View\LayoutFactory $layoutFactory
     * @param Logger $logger
     * @param \Magento\Backend\App\Action\Context $context
     */
    function __construct(
        Selected $updaterSelected,
        LayoutFactory $layoutFactory,
        Logger $logger,
        Action\Context $context
    ) {
        $this->updaterSelected = $updaterSelected;
        $this->layoutFactory = $layoutFactory;
        $this->logger = $logger;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\Controller\Result\Json
     */
    public function execute()
    {
        $response = ['status' => 'success'];
        try {
            $this->updaterSelected->update();
            $response['message'] = __('Database(s) successfully updated.');
            $response['status_info'] = $this->getStatusHtml();
        } catch (LocalizedException $e) {
            $response['status'] = 'error';
            $response['message'] = $e->getMessage();
        } catch (\Exception $e) {
            $this->logger->critical($e);
            $response['status'] = 'error';
            $response['message'] = __('An error occurred during DB updating.');
        }

        /** @var Json $resultJson */
        $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        $resultJson->setData($response);
        return $resultJson;
    }

    /**
     * @return string
     */
    protected function getStatusHtml()
    {
        $layout = $this->layoutFactory->create();

        /** @var \Tobai\GeoIp2\Block\Adminhtml\System\Config\Status $statusBlock */
        $statusBlock = $layout->createBlock('Tobai\GeoIp2\Block\Adminhtml\System\Config\Status');
        return $statusBlock->getDbStatus();
    }

    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Tobai_GeoIp2::config_tobai_geoip2');
    }
}

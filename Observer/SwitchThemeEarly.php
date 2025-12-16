<?php
/**
 * Copyright © Pointeger. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Pointeger\ThemeSwitcher\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\View\DesignInterface;
use Magento\Store\Model\StoreManagerInterface;
use Pointeger\ThemeSwitcher\Helper\Data as Helper;
use Psr\Log\LoggerInterface;

class SwitchThemeEarly implements ObserverInterface
{
    /**
     * @var Helper
     */
    private $helper;

    /**
     * @var DesignInterface
     */
    private $design;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param Helper $helper
     * @param DesignInterface $design
     * @param StoreManagerInterface $storeManager
     * @param LoggerInterface $logger
     */
    public function __construct(
        Helper $helper,
        DesignInterface $design,
        StoreManagerInterface $storeManager,
        LoggerInterface $logger
    ) {
        $this->helper = $helper;
        $this->design = $design;
        $this->storeManager = $storeManager;
        $this->logger = $logger;
    }

    /**
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer): void
    {
        try {
            $request = $observer->getEvent()->getRequest();

            if (!$request) {
                return;
            }

            $moduleName = $request->getModuleName();
            $controllerName = $request->getControllerName();
            $actionName = $request->getActionName();

            if (empty($moduleName) || empty($controllerName) || empty($actionName)) {
                return;
            }

            $fullActionName = strtolower($moduleName . '_' . $controllerName . '_' . $actionName);

            if ($fullActionName === '__' || $fullActionName === '') {
                return;
            }

            $storeId = $this->storeManager->getStore()->getId();
            $themeCode = $this->helper->getThemeCodeForHandle($fullActionName, $storeId);

            if (!$themeCode) {
                return;
            }

            $currentTheme = $this->design->getDesignTheme();
            if ($currentTheme && $currentTheme->getCode() === $themeCode) {
                return;
            }

            $this->design->setDesignTheme($themeCode, 'frontend');

        } catch (\Exception $e) {
            $this->logger->error(
                'Pointeger_ThemeSwitcher: Error switching theme early',
                ['exception' => $e->getMessage(), 'trace' => $e->getTraceAsString()]
            );
        }
    }
}

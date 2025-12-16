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

class SwitchTheme implements ObserverInterface
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
            $eventData = $observer->getEvent()->getData();
            $fullActionName = $eventData['full_action_name'] ?? null;

            if (!$fullActionName) {
                return;
            }

            $storeId = $this->storeManager->getStore()->getId();
            $themeCode = $this->helper->getThemeCodeForHandle($fullActionName, $storeId);

            if (!$themeCode) {
                return;
            }

            $this->design->setDesignTheme($themeCode, 'frontend');

        } catch (\Exception $e) {
            $this->logger->error(
                'Pointeger_ThemeSwitcher: Error switching theme',
                ['exception' => $e->getMessage(), 'trace' => $e->getTraceAsString()]
            );
        }
    }
}

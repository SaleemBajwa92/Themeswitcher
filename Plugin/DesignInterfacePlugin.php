<?php
/**
 * Copyright © Pointeger. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Pointeger\ThemeSwitcher\Plugin;

use Magento\Framework\View\DesignInterface;
use Magento\Framework\App\Request\Http as HttpRequest;
use Pointeger\ThemeSwitcher\Helper\Data as Helper;
use Psr\Log\LoggerInterface;

class DesignInterfacePlugin
{
    /**
     * @var Helper
     */
    private $helper;

    /**
     * @var HttpRequest
     */
    private $request;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var bool
     */
    private $isProcessing = false;

    /**
     * @param Helper $helper
     * @param HttpRequest $request
     * @param LoggerInterface $logger
     */
    public function __construct(
        Helper $helper,
        HttpRequest $request,
        LoggerInterface $logger
    ) {
        $this->helper = $helper;
        $this->request = $request;
        $this->logger = $logger;
    }

    /**
     * @param DesignInterface $subject
     * @param \Closure $proceed
     * @param string|null $area
     * @param array $params
     * @return string
     */
    public function aroundGetConfigurationDesignTheme(
        DesignInterface $subject,
        \Closure $proceed,
        $area = null,
        $params = []
    ) {
        if ($this->isProcessing) {
            return $proceed($area, $params);
        }

        try {
            $this->isProcessing = true;

            if ($area !== 'frontend' && $area !== null) {
                return $proceed($area, $params);
            }

            $moduleName = $this->request->getModuleName();
            $controllerName = $this->request->getControllerName();
            $actionName = $this->request->getActionName();

            if (empty($moduleName) || empty($controllerName) || empty($actionName)) {
                return $proceed($area, $params);
            }

            $fullActionName = strtolower($moduleName . '_' . $controllerName . '_' . $actionName);
            $themeCode = $this->helper->getThemeCodeForHandle($fullActionName);

            if ($themeCode) {
                return $themeCode;
            }

        } catch (\Exception $e) {
            $this->logger->error(
                'Pointeger_ThemeSwitcher: Error in theme resolution plugin',
                ['exception' => $e->getMessage(), 'trace' => $e->getTraceAsString()]
            );
        } finally {
            $this->isProcessing = false;
        }

        return $proceed($area, $params);
    }
}

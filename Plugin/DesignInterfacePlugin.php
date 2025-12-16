<?php

declare(strict_types=1);

namespace Pointeger\ThemeSwitcher\Plugin;

use Magento\Framework\View\DesignInterface;
use Magento\Framework\App\RequestInterface;
use Pointeger\ThemeSwitcher\Helper\Data as Helper;
use Psr\Log\LoggerInterface;

class DesignInterfacePlugin
{
    /**
     * @var Helper
     */
    private $helper;

    /**
     * @var RequestInterface
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
     * @param RequestInterface $request
     * @param LoggerInterface $logger
     */
    public function __construct(
        Helper $helper,
        RequestInterface $request,
        LoggerInterface $logger
    ) {
        $this->helper = $helper;
        $this->request = $request;
        $this->logger = $logger;
    }

    /**
     * Intercept theme configuration to switch theme based on layout handle
     *
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
        // Prevent recursion
        if ($this->isProcessing) {
            return $proceed($area, $params);
        }

        try {
            $this->isProcessing = true;

            if ($area !== 'frontend' && $area !== null) {
                return $proceed($area, $params);
            }

            $fullActionName = $this->request->getFullActionName();

            if (empty($fullActionName)) {
                return $proceed($area, $params);
            }

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


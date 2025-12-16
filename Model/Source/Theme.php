<?php
/**
 * Copyright © Pointeger. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Pointeger\ThemeSwitcher\Model\Source;

use Magento\Framework\Data\OptionSourceInterface;
use Magento\Theme\Model\ResourceModel\Theme\CollectionFactory as ThemeCollectionFactory;
use Psr\Log\LoggerInterface;

class Theme implements OptionSourceInterface
{
    /**
     * @var ThemeCollectionFactory
     */
    private $themeCollectionFactory;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var array|null
     */
    private $options = null;

    /**
     * @param ThemeCollectionFactory $themeCollectionFactory
     * @param LoggerInterface $logger
     */
    public function __construct(
        ThemeCollectionFactory $themeCollectionFactory,
        LoggerInterface $logger
    ) {
        $this->themeCollectionFactory = $themeCollectionFactory;
        $this->logger = $logger;
    }

    /**
     * @return array
     */
    public function toOptionArray(): array
    {
        if ($this->options !== null) {
            return $this->options;
        }

        $this->options = [
            ['value' => '', 'label' => __('-- Please Select --')]
        ];

        try {
            $themeCollection = $this->themeCollectionFactory->create();
            $themeCollection->addFilter('area', 'frontend');
            $themeCollection->getSelect()->order('theme_title ASC');

            foreach ($themeCollection as $theme) {
                $themeCode = $theme->getCode();
                $themeTitle = $theme->getThemeTitle();
                $label = !empty($themeTitle) ? $themeTitle : $themeCode;

                $this->options[] = [
                    'value' => $themeCode,
                    'label' => $label . ' (' . $themeCode . ')'
                ];
            }
        } catch (\Exception $e) {
            $this->logger->error(
                'Pointeger_ThemeSwitcher: Error loading themes',
                ['exception' => $e->getMessage()]
            );
        }

        return $this->options;
    }
}

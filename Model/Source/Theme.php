<?php

declare(strict_types=1);

namespace Pointeger\ThemeSwitcher\Model\Source;

use Magento\Framework\Data\OptionSourceInterface;
use Magento\Theme\Model\ResourceModel\Theme\CollectionFactory as ThemeCollectionFactory;
use Psr\Log\LoggerInterface;

class Theme implements OptionSourceInterface
{
    /**
     * @var \Magento\Theme\Model\ResourceModel\Theme\CollectionFactory
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
     * @param \Magento\Theme\Model\ResourceModel\Theme\CollectionFactory $themeCollectionFactory
     * @param LoggerInterface $logger
     */
    public function __construct(
        ThemeCollectionFactory $themeCollectionFactory,
        LoggerInterface        $logger
    )
    {
        $this->themeCollectionFactory = $themeCollectionFactory;
        $this->logger = $logger;
    }

    /**
     * Return array of options as value-label pairs
     *
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
            /** @var \Magento\Theme\Model\ResourceModel\Theme\Collection $themeCollection */
            $themeCollection = $this->themeCollectionFactory->create();

            $themeCollection->addFilter('area', 'frontend');
            $themeCollection->getSelect()->order('theme_title ASC');

            /** @var \Magento\Framework\View\Design\ThemeInterface $theme */
            foreach ($themeCollection as $theme) {
                $themeCode = $theme->getCode();

                /** @phpstan-ignore-next-line */
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

<?php

declare(strict_types=1);

namespace Pointeger\ThemeSwitcher\Block\Adminhtml\System\Config\Form\Field;

use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Pointeger\ThemeSwitcher\Model\Source\Theme as ThemeSource;
use Pointeger\ThemeSwitcher\Model\Source\LayoutHandles as LayoutHandlesSource;
use Magento\Framework\Json\EncoderInterface;
use Magento\Framework\View\Asset\Repository as AssetRepository;

class ThemeMappings extends Field
{
    /**
     * @var ThemeSource
     */
    private $themeSource;

    /**
     * @var LayoutHandlesSource
     */
    private $layoutHandlesSource;

    /**
     * @var EncoderInterface
     */
    private $jsonEncoder;

    /**
     * @var AssetRepository
     */
    private $assetRepository;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param ThemeSource $themeSource
     * @param LayoutHandlesSource $layoutHandlesSource
     * @param EncoderInterface $jsonEncoder
     * @param AssetRepository $assetRepository
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        ThemeSource $themeSource,
        LayoutHandlesSource $layoutHandlesSource,
        EncoderInterface $jsonEncoder,
        AssetRepository $assetRepository,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->themeSource = $themeSource;
        $this->layoutHandlesSource = $layoutHandlesSource;
        $this->jsonEncoder = $jsonEncoder;
        $this->assetRepository = $assetRepository;
    }

    /**
     * @param AbstractElement $element
     * @return string
     */
    protected function _getElementHtml(AbstractElement $element): string
    {
        $element->setType('hidden');
        $elementId = $element->getHtmlId();
        $elementName = $element->getName();
        $elementValue = $element->getValue() ?: '[]';

        // Get theme options
        $themeOptions = [];
        foreach ($this->themeSource->toOptionArray() as $option) {
            if (!empty($option['value'])) {
                $themeOptions[$option['value']] = $option['label'];
            }
        }

        // Get handle options
        $handleOptions = [];
        foreach ($this->layoutHandlesSource->toOptionArray() as $option) {
            $handleOptions[$option['value']] = $option['label'];
        }

        $html = '<div id="pointeger_themeswitcher_mappings_container">';
        $html .= '<input type="hidden" id="' . $elementId . '" name="' . $elementName . '" value="' . $this->escapeHtml($elementValue) . '"/>';
        $html .= '<div id="pointeger_themeswitcher_mappings_list"></div>';
        $html .= '<button type="button" id="pointeger_themeswitcher_add_mapping" class="action-secondary">' . __('Add Theme Mapping') . '</button>';
        $html .= '</div>';

        $html .= '<script type="text/javascript">
            require(["jquery", "pointeger_themeswitcher_mappings"], function($, mappings) {
                mappings.init({
                    containerId: "pointeger_themeswitcher_mappings_container",
                    listId: "pointeger_themeswitcher_mappings_list",
                    inputId: "' . $elementId . '",
                    addButtonId: "pointeger_themeswitcher_add_mapping",
                    existingData: ' . $elementValue . ',
                    themeOptions: ' . $this->jsonEncoder->encode($themeOptions) . ',
                    handleOptions: ' . $this->jsonEncoder->encode($handleOptions) . '
                });
            });
        </script>';

        return $html;
    }
}


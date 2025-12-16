<?php
/**
 * Copyright © Pointeger. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Pointeger\ThemeSwitcher\Block\Adminhtml\System\Config\Form\Field;

use Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray;
use Magento\Framework\Data\Form\Element\Factory;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\View\Helper\SecureHtmlRenderer;
use Magento\Framework\Serialize\Serializer\Json;

class CustomHandles extends AbstractFieldArray
{
    /**
     * @var Factory
     */
    private $elementFactory;

    /**
     * @var Json
     */
    private $json;

    /**
     * @param Context $context
     * @param Factory $elementFactory
     * @param Json $json
     * @param array $data
     * @param SecureHtmlRenderer|null $secureRenderer
     */
    public function __construct(
        Context $context,
        Factory $elementFactory,
        Json $json,
        array $data = [],
        ?SecureHtmlRenderer $secureRenderer = null
    ) {
        $this->elementFactory = $elementFactory;
        $this->json = $json;
        parent::__construct($context, $data, $secureRenderer);
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->addColumn('handle', [
            'label' => __('Layout Handle Name'),
            'class' => 'required-entry',
        ]);

        $this->_addAfter = false;
        $this->_addButtonLabel = __('Add Handle');

        parent::_construct();
    }

    /**
     * @return array
     */
    public function getArrayRows()
    {
        $element = $this->getElement();
        $value = $element->getValue();

        if (is_string($value) && !empty($value)) {
            try {
                $decoded = $this->json->unserialize($value);
                if (is_array($decoded)) {
                    $formattedValue = $this->formatArrayValue($decoded);
                    $element->setValue($formattedValue);
                } else {
                    $element->setValue([]);
                }
            } catch (\Exception $e) {
                $element->setValue([]);
            }
        } elseif (is_array($value)) {
            $formattedValue = $this->formatArrayValue($value);
            $element->setValue($formattedValue);
        } else {
            $element->setValue([]);
        }

        return parent::getArrayRows();
    }

    /**
     * @param array $value
     * @return array
     */
    private function formatArrayValue(array $value): array
    {
        if (empty($value)) {
            return [];
        }

        $formatted = [];
        
        foreach ($value as $key => $row) {
            if ($row === null || $key === '__empty') {
                continue;
            }
            
            if (is_string($row) && !empty(trim($row))) {
                $rowId = is_string($key) && !is_numeric($key) ? $key : '_' . uniqid();
                $formatted[$rowId] = ['handle' => trim($row)];
            } elseif (is_array($row)) {
                if (isset($row['handle']) && !empty(trim($row['handle']))) {
                    $rowId = is_string($key) && !is_numeric($key) ? $key : '_' . uniqid();
                    $formatted[$rowId] = ['handle' => trim($row['handle'])];
                } elseif (!empty($row)) {
                    $handleValue = reset($row);
                    if (!empty($handleValue) && is_string($handleValue) && !empty(trim($handleValue))) {
                        $rowId = is_string($key) && !is_numeric($key) ? $key : '_' . uniqid();
                        $formatted[$rowId] = ['handle' => trim($handleValue)];
                    }
                }
            }
        }

        return $formatted;
    }
}

<?php
/**
 * Copyright © Pointeger. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Pointeger\ThemeSwitcher\Model\Config\Backend;

use Magento\Framework\App\Cache\TypeListInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Config\Value;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Registry;
use Magento\Framework\Serialize\Serializer\Json;

class CustomHandles extends Value
{
    /**
     * @var Json
     */
    private $json;

    /**
     * @param Context $context
     * @param Registry $registry
     * @param ScopeConfigInterface $config
     * @param TypeListInterface $cacheTypeList
     * @param AbstractResource $resource
     * @param AbstractDb $resourceCollection
     * @param Json $json
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        ScopeConfigInterface $config,
        TypeListInterface $cacheTypeList,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        Json $json = null,
        array $data = []
    ) {
        $this->json = $json ?: ObjectManager::getInstance()->get(Json::class);
        parent::__construct($context, $registry, $config, $cacheTypeList, $resource, $resourceCollection, $data);
    }

    /**
     * @return CustomHandles
     */
    public function beforeSave()
    {
        $value = $this->getValue();

        if (is_array($value)) {
            $filteredValue = $this->filterEmptyRows($value);
            if (!empty($filteredValue)) {
                $this->setValue($this->json->serialize($filteredValue));
            } else {
                $this->setValue('');
            }
        } elseif (is_string($value) && !empty($value)) {
            try {
                $decoded = $this->json->unserialize($value);
                if (is_array($decoded)) {
                    $filteredValue = $this->filterEmptyRows($decoded);
                    if (!empty($filteredValue)) {
                        $this->setValue($this->json->serialize($filteredValue));
                    } else {
                        $this->setValue('');
                    }
                } else {
                    $this->setValue('');
                }
            } catch (\Exception $e) {
                $this->setValue('');
            }
        } else {
            $this->setValue('');
        }

        return parent::beforeSave();
    }

    /**
     * @param array $value
     * @return array
     */
    private function filterEmptyRows(array $value): array
    {
        $filtered = [];
        foreach ($value as $key => $row) {
            if ($key === '__empty') {
                continue;
            }

            if (is_array($row) && isset($row['handle'])) {
                $handleValue = trim($row['handle']);
                if (!empty($handleValue)) {
                    $filtered[$key] = [
                        'handle' => $handleValue
                    ];
                }
            } elseif (is_string($row)) {
                $handleValue = trim($row);
                if (!empty($handleValue)) {
                    $filtered[$key] = [
                        'handle' => $handleValue
                    ];
                }
            }
        }
        return $filtered;
    }

    /**
     * @return CustomHandles
     */
    public function afterLoad()
    {
        $value = $this->getValue();
        if (is_string($value) && !empty($value)) {
            try {
                $decoded = $this->json->unserialize($value);
                if (is_array($decoded)) {
                    $this->setValue($decoded);
                }
            } catch (\Exception $e) {
                $this->setValue([]);
            }
        } elseif (empty($value)) {
            $this->setValue([]);
        }
        return parent::afterLoad();
    }
}

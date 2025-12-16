<?php
/**
 * Copyright © Pointeger. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Pointeger\ThemeSwitcher\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\ScopeInterface;

class Data extends AbstractHelper
{
    const XML_PATH_THEME_MAPPINGS = 'pointeger_themeswitcher/theme_configuration/theme_mappings';
    const XML_PATH_CUSTOM_HANDLES = 'pointeger_themeswitcher/custom_handles/custom_layout_handles';

    /**
     * @param int|null $storeId
     * @return array
     */
    public function getThemeMappings($storeId = null): array
    {
        $mappingsJson = $this->scopeConfig->getValue(
            self::XML_PATH_THEME_MAPPINGS,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );

        if (empty($mappingsJson)) {
            return [];
        }

        if (is_string($mappingsJson)) {
            $mappings = json_decode($mappingsJson, true);
            return is_array($mappings) ? $mappings : [];
        }

        return is_array($mappingsJson) ? $mappingsJson : [];
    }

    /**
     * @param string $handle
     * @param int|null $storeId
     * @return bool
     */
    public function shouldUseCustomTheme(string $handle, $storeId = null): bool
    {
        $themeCode = $this->getThemeCodeForHandle($handle, $storeId);
        return $themeCode !== null;
    }

    /**
     * @param string $handle
     * @param int|null $storeId
     * @return string|null
     */
    public function getThemeCodeForHandle(string $handle, $storeId = null): ?string
    {
        $mappings = $this->getThemeMappings($storeId);

        foreach ($mappings as $mapping) {
            if (empty($mapping['theme']) || empty($mapping['handles'])) {
                continue;
            }

            $handles = is_array($mapping['handles'])
                ? $mapping['handles']
                : (is_string($mapping['handles']) ? explode(',', $mapping['handles']) : []);

            if (in_array($handle, $handles, true)) {
                return $mapping['theme'];
            }
        }

        return null;
    }

    /**
     * @param int|null $storeId
     * @return string|null
     */
    public function getThemeCode($storeId = null): ?string
    {
        $mappings = $this->getThemeMappings($storeId);
        if (!empty($mappings) && !empty($mappings[0]['theme'])) {
            return $mappings[0]['theme'];
        }

        return null;
    }

    /**
     * @param int|null $storeId
     * @return array
     */
    public function getCustomHandles($storeId = null): array
    {
        $customHandlesJson = $this->scopeConfig->getValue(
            self::XML_PATH_CUSTOM_HANDLES,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );

        if (empty($customHandlesJson)) {
            return [];
        }

        if (is_string($customHandlesJson)) {
            $customHandles = json_decode($customHandlesJson, true);
            if (is_array($customHandles)) {
                $handles = [];
                foreach ($customHandles as $handleData) {
                    if (isset($handleData['handle']) && !empty(trim($handleData['handle']))) {
                        $handles[] = trim($handleData['handle']);
                    }
                }
                return array_filter($handles);
            }
        }

        return [];
    }
}

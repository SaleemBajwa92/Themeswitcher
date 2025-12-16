<?php

declare(strict_types=1);

namespace Pointeger\ThemeSwitcher\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\ScopeInterface;

class Data extends AbstractHelper
{
    const XML_PATH_THEME_MAPPINGS = 'pointeger_themeswitcher/theme_configuration/theme_mappings';

    /**
     * Get all theme mappings
     *
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
     * Check if handle should use custom theme
     *
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
     * Get theme code for a specific handle
     *
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
     * Get theme code based on selected theme (backward compatibility)
     * Now uses the new mapping system
     *
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
}


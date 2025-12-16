# Pointeger Theme Switcher for Magento 2

A Magento 2 extension that allows you to switch themes dynamically based on layout XML handles. This module enables you to assign different themes to specific pages or layout handles, providing flexible theme management for your Magento store.

## Features

- Switch themes based on layout handles
- Multiple theme mappings support
- Store-level configuration
- Easy admin panel configuration
- Compatible with Magento 2.4.x

## Installation

### Via Composer from GitHub

Add the repository to your `composer.json`:

```json
{
    "repositories": [
        {
            "type": "vcs",
            "url": "https://github.com/SaleemBajwa92/Themeswitcher.git"
        }
    ],
    "require": {
        "pointeger/magento-2-theme-switcher": "dev-main"
    }
}
```

Then run:
```bash
composer require pointeger/magento-2-theme-switcher:dev-main
```

### Manual Installation

1. Clone the repository:
   ```bash
   git clone https://github.com/SaleemBajwa92/Themeswitcher.git app/code/Pointeger/ThemeSwitcher
   ```

2. Or download and extract to `app/code/Pointeger/ThemeSwitcher/`

3. Run the following commands:

```bash
php bin/magento module:enable Pointeger_ThemeSwitcher
php bin/magento setup:upgrade
php bin/magento cache:flush
```

## Configuration

1. Navigate to **Stores > Configuration > General > Theme Switcher**
2. Click "Add Theme Mapping" to create theme mappings
3. Configure which theme should be used for specific layout XML handles
4. Save the configuration

## Requirements

- Magento 2.4.x or higher
- PHP 7.4 or higher

## Support

For support, please contact: support@pointeger.com

## Repository

GitHub: https://github.com/SaleemBajwa92/Themeswitcher

## License

Proprietary


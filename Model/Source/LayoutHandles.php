<?php

declare(strict_types=1);

namespace Pointeger\ThemeSwitcher\Model\Source;

use Magento\Framework\Data\OptionSourceInterface;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem\Directory\ReadFactory;
use Magento\Framework\App\Utility\Files;

class LayoutHandles implements OptionSourceInterface
{
    /**
     * @var ReadFactory
     */
    private $readFactory;

    /**
     * @var array|null
     */
    private $handles = null;

    /**
     * @param ReadFactory $readFactory
     */
    public function __construct(
        ReadFactory $readFactory
    ) {
        $this->readFactory = $readFactory;
    }

    /**
     * Return array of options as value-label pairs
     *
     * @return array
     */
    public function toOptionArray(): array
    {
        $options = [];
        $handles = $this->getLayoutHandles();

        foreach ($handles as $handle) {
            $options[] = [
                'value' => $handle,
                'label' => $handle
            ];
        }

        return $options;
    }

    /**
     * Get common layout handles from frontend area
     *
     * @return array
     */
    private function getLayoutHandles(): array
    {
        if ($this->handles === null) {
            // Common Magento 2 frontend layout handles
            $this->handles = [
                'default',
                'cms_index_index',
                'cms_page_view',
                'catalog_category_view',
                'catalog_product_view',
                'catalogsearch_result_index',
                'checkout_index_index',
                'checkout_cart_index',
                'customer_account_login',
                'customer_account_create',
                'customer_account_index',
                'customer_account_edit',
                'customer_address_index',
                'customer_orders_index',
                'wishlist_index_index',
                'contact_index_index',
                'catalogsearch_advanced_index',
                'catalogsearch_advanced_result',
                'sales_order_view',
                'sales_order_history',
                'sales_order_invoice',
                'sales_order_shipment',
                'sales_order_print',
                'sales_guest_view',
                'sales_guest_form',
                'sales_guest_print',
                'catalog_product_compare_index',
                'catalog_product_compare_list',
                'newsletter_manage_index',
                'review_product_list',
                'review_product_view',
                'review_customer_index',
                'paypal_express_review',
                'multishipping_checkout_overview',
                'multishipping_checkout_addresses',
                'multishipping_checkout_shipping',
                'multishipping_checkout_billing',
                'multishipping_checkout_success',
                'onestepcheckout_index_index',
            ];

            sort($this->handles);
        }

        return $this->handles;
    }
}


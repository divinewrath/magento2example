<?php
declare(strict_types=1);

namespace MacoOnboarding\CustomShippingModule\Provider;

use MacoOnboarding\CustomShippingModule\Constants;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;

class ShippingCostProvider
{
    private ShippingPricesConfigProvider $shippingPricesConfigProvider;
    private ScopeConfigInterface $scopeConfig;

    public function __construct(
        ShippingPricesConfigProvider $shippingPricesConfigProvider,
        ScopeConfigInterface         $scopeConfig
    ) {
        $this->shippingPricesConfigProvider = $shippingPricesConfigProvider;
        $this->scopeConfig = $scopeConfig;
    }

    public function getShippingCost(CustomerInterface $customer)
    {
        $customerPrice = $this->getCustomerPrice($customer);
        $groupPrice = $this->getCustomerGroupPrice($customer);
        $defaultPrice = $this->getDefaultPrice();

        $prices = array_filter([
            $customerPrice,
            $groupPrice,
            $defaultPrice
        ], static function ($price) {
            return is_string($price) && is_numeric($price);
        });

        usort($prices, static function ($a, $b) {
            return bccomp($a, $b, 2);
        });

        return $prices[0] ?? null;
    }

    protected function getCustomerGroupPrice(CustomerInterface $customer)
    {
        $groupId = $customer->getGroupId();
        $groupPrices = $this->shippingPricesConfigProvider->getGroupPrices();

        return $groupPrices[$groupId] ?? null;
    }

    protected function getCustomerPrice(CustomerInterface $customer)
    {
        $customAttributes = $customer->getCustomAttributes();
        if (array_key_exists(Constants::ATTR_CUSTOM_SHIPPING_PRICE, $customAttributes)) {
            return $customer->getCustomAttribute(Constants::ATTR_CUSTOM_SHIPPING_PRICE)->getValue();
        }

        return null;
    }

    protected function getDefaultPrice(): string
    {
        return (string)$this->scopeConfig->getValue(Constants::XML_PATH_DEFAULT_SHIPPING_PRICE);
    }
}

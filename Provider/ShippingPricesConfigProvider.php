<?php
declare(strict_types=1);

namespace MacoOnboarding\CustomShippingModule\Provider;

use MacoOnboarding\CustomShippingModule\Constants;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Serialize\Serializer\Json as JsonSerializer;

class ShippingPricesConfigProvider
{
    private ScopeConfigInterface $scopeConfig;
    private JsonSerializer $jsonSerializer;

    public function __construct(
        ScopeConfigInterface $scopeConfig,
        JsonSerializer       $jsonSerializer
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->jsonSerializer = $jsonSerializer;
    }

    public function getGroupPrices(): array
    {
        $groupPrices = $this->scopeConfig->getValue(Constants::XML_PATH_CUSTOM_SHIPPING_GROUP_PRICES);
        try {
            $groupPrices = $this->jsonSerializer->unserialize($groupPrices);
        } catch (\InvalidArgumentException $e) {
            return [];
        }

        return array_column($groupPrices, 'price', 'customer_groups');
    }
}

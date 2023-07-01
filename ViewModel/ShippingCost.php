<?php
declare(strict_types=1);

namespace MacoOnboarding\CustomShippingModule\ViewModel;

use MacoOnboarding\CustomShippingModule\Provider\CustomerDataProvider;
use MacoOnboarding\CustomShippingModule\Provider\ShippingCostProvider;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;

class ShippingCost implements ArgumentInterface
{
    private CustomerSession $customerSession;
    private ShippingCostProvider $shippingCostProvider;
    private CustomerDataProvider $customerDataProvider;
    private PriceCurrencyInterface $priceCurrency;

    public function __construct(
        CustomerSession $customerSession,
        ShippingCostProvider $shippingCostProvider,
        CustomerDataProvider $customerDataProvider,
        PriceCurrencyInterface $priceCurrency
    ) {
        $this->customerSession = $customerSession;
        $this->shippingCostProvider = $shippingCostProvider;
        $this->customerDataProvider = $customerDataProvider;
        $this->priceCurrency = $priceCurrency;
    }

    public function getShippingCost(): string
    {
        if ($this->customerSession->isLoggedIn()) {
            $customerId = (int)$this->customerSession->getCustomerId();
            $customer = $this->customerDataProvider->getCustomerById($customerId);

            return (string)$this->shippingCostProvider->getShippingCost($customer);
        }

        return $this->shippingCostProvider->getDefaultPrice();
    }

    public function getFormattedPrice(string $price)
    {
        return $this->priceCurrency->convertAndFormat($price, false);
    }
}

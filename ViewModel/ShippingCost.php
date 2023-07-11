<?php
declare(strict_types=1);

namespace MacoOnboarding\CustomShippingModule\ViewModel;

use MacoOnboarding\CustomShippingModule\Provider\CustomerDataProvider;
use MacoOnboarding\CustomShippingModule\Provider\ShippingCostProvider;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;

class ShippingCost implements ArgumentInterface
{
    /**
     * @var CustomerSession
     */
    private CustomerSession $customerSession;

    /**
     * @var ShippingCostProvider
     */
    private ShippingCostProvider $shippingCostProvider;

    /**
     * @var CustomerDataProvider
     */
    private CustomerDataProvider $customerDataProvider;

    /**
     * @var PriceCurrencyInterface
     */
    private PriceCurrencyInterface $priceCurrency;

    /**
     * @param CustomerSession $customerSession
     * @param ShippingCostProvider $shippingCostProvider
     * @param CustomerDataProvider $customerDataProvider
     * @param PriceCurrencyInterface $priceCurrency
     */
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

    /**
     * @return string
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function getShippingCost(): string
    {
        if ($this->customerSession->isLoggedIn()) {
            $customerId = (int)$this->customerSession->getCustomerId();
            $customer = $this->customerDataProvider->getCustomerById($customerId);

            return (string)$this->shippingCostProvider->getShippingCost($customer);
        }

        return $this->shippingCostProvider->getDefaultPrice();
    }

    /**
     * @param string $price
     * @return string
     */
    public function getFormattedPrice(string $price): string
    {
        return $this->priceCurrency->convertAndFormat($price, false);
    }
}

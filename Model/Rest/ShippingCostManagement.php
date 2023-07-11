<?php
declare(strict_types=1);

namespace MacoOnboarding\CustomShippingModule\Model\Rest;

use MacoOnboarding\CustomShippingModule\Api\Data\ShippingCostInterface;
use MacoOnboarding\CustomShippingModule\Api\Data\ShippingCostInterfaceFactory;
use MacoOnboarding\CustomShippingModule\Api\ShippingCostManagementInterface;
use MacoOnboarding\CustomShippingModule\Provider\CustomerDataProvider;
use MacoOnboarding\CustomShippingModule\Provider\ShippingCostProvider;
use Magento\Framework\Exception\NoSuchEntityException;

class ShippingCostManagement implements ShippingCostManagementInterface
{
    /**
     * @var ShippingCostInterfaceFactory
     */
    private ShippingCostInterfaceFactory $shippingCostFactory;

    /**
     * @var ShippingCostProvider
     */
    private ShippingCostProvider $shippingCostProvider;

    /**
     * @var CustomerDataProvider
     */
    private CustomerDataProvider $customerDataProvider;

    /**
     * @param ShippingCostInterfaceFactory $shippingCostFactory
     * @param ShippingCostProvider $shippingCostProvider
     * @param CustomerDataProvider $customerDataProvider
     */
    public function __construct(
        ShippingCostInterfaceFactory $shippingCostFactory,
        ShippingCostProvider         $shippingCostProvider,
        CustomerDataProvider         $customerDataProvider
    ) {
        $this->shippingCostFactory = $shippingCostFactory;
        $this->shippingCostProvider = $shippingCostProvider;
        $this->customerDataProvider = $customerDataProvider;
    }

    /**
     * @param string $email
     * @param int $customerId
     * @return ShippingCostInterface
     * @throws NoSuchEntityException
     */
    public function getShippingCost(string $email, int $customerId): ShippingCostInterface
    {
        $loggedInCustomer = $this->customerDataProvider->getCustomerById($customerId);

        if ($loggedInCustomer->getEmail() !== $email) {
            throw new NoSuchEntityException(__('Customer not found'));
        }

        $customer = $this->customerDataProvider->getCustomerByEmail($email);

        $price = $this->shippingCostProvider->getShippingCost($customer);
        if ($price) {
            /** @var ShippingCostInterface $result */
            $result = $this->shippingCostFactory->create();

            return $result->setShippingCost((string)$price);
        }

        throw new NoSuchEntityException(__('Not found'));
    }
}

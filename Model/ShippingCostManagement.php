<?php
declare(strict_types=1);

namespace MacoOnboarding\CustomShippingModule\Model;

use MacoOnboarding\CustomShippingModule\Api\Data\ShippingCostInterfaceFactory;
use MacoOnboarding\CustomShippingModule\Api\Data\ShippingCostInterface;
use MacoOnboarding\CustomShippingModule\Api\ShippingCostManagementInterface;
use MacoOnboarding\CustomShippingModule\Constants;
use MacoOnboarding\CustomShippingModule\Provider\ShippingCostProvider;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Exception\NoSuchEntityException;

class ShippingCostManagement implements ShippingCostManagementInterface
{
    private ShippingCostInterfaceFactory $shippingCostFactory;
    private CustomerRepositoryInterface $customerRepository;
    private SearchCriteriaBuilder $searchCriteriaBuilder;
    private ShippingCostProvider $shippingCostProvider;

    public function __construct(
        ShippingCostInterfaceFactory $shippingCostFactory,
        CustomerRepositoryInterface $customerRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        ShippingCostProvider $shippingCostProvider
    ) {
        $this->shippingCostFactory = $shippingCostFactory;
        $this->customerRepository = $customerRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->shippingCostProvider = $shippingCostProvider;
    }

    /**
     * @param string $email
     * @param int $customerId
     * @return ShippingCostInterface
     * @throws NoSuchEntityException
     */
    public function getShippingCost(string $email, int $customerId): ShippingCostInterface
    {
        $loggedInCustomer = $this->getCustomerById($customerId);

        if ($loggedInCustomer->getEmail() !== $email) {
            throw new NoSuchEntityException(__('Customer not found'));
        }

        $customer = $this->getCustomerByEmail($email);

        $price = $this->shippingCostProvider->getShippingCost($customer);
        if ($price) {
            /** @var ShippingCostInterface $result */
            $result = $this->shippingCostFactory->create();

            return $result->setShippingCost((string)$price);
        }

        throw new NoSuchEntityException(__('Not found'));
    }

    protected function getCustomerByEmail(string $email): CustomerInterface
    {
        $searchCriteria = $this->searchCriteriaBuilder->addFilter('email', $email)->create();
        $result = $this->customerRepository->getList($searchCriteria);
        $customers = $result->getItems();

        if (!count($customers)) {
            throw new NoSuchEntityException(__('Customer not found'));
        }

        return array_pop($customers);
    }

    protected function getCustomerById(int $customerId): CustomerInterface
    {
        return $this->customerRepository->getById($customerId);
    }
}

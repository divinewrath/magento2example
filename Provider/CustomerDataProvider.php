<?php
declare(strict_types=1);

namespace MacoOnboarding\CustomShippingModule\Provider;

use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Exception\NoSuchEntityException;

class CustomerDataProvider
{
    private SearchCriteriaBuilder $searchCriteriaBuilder;
    private CustomerRepositoryInterface $customerRepository;

    public function __construct(
        SearchCriteriaBuilder $searchCriteriaBuilder,
        CustomerRepositoryInterface $customerRepository
    ) {
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->customerRepository = $customerRepository;
    }

    public function getCustomerByEmail(string $email): CustomerInterface
    {
        $searchCriteria = $this->searchCriteriaBuilder->addFilter('email', $email)->create();
        $result = $this->customerRepository->getList($searchCriteria);
        $customers = $result->getItems();

        if (!count($customers)) {
            throw new NoSuchEntityException(__('Customer not found'));
        }

        return array_pop($customers);
    }

    public function getCustomerById(int $customerId): CustomerInterface
    {
        return $this->customerRepository->getById($customerId);
    }
}

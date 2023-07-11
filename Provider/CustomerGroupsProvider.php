<?php
declare(strict_types=1);

namespace MacoOnboarding\CustomShippingModule\Provider;

use Magento\Customer\Model\Group;
use Magento\Customer\Model\ResourceModel\Group\Collection as CustomerGroupCollection;
use Magento\Customer\Model\ResourceModel\Group\CollectionFactory as CustomerGroupCollectionFactory;

class CustomerGroupsProvider
{
    /**
     * @var CustomerGroupCollectionFactory
     */
    private CustomerGroupCollectionFactory $customerGroupCollectionFactory;

    /**
     * @param CustomerGroupCollectionFactory $customerGroupCollectionFactory
     */
    public function __construct(CustomerGroupCollectionFactory $customerGroupCollectionFactory)
    {
        $this->customerGroupCollectionFactory = $customerGroupCollectionFactory;
    }

    /**
     * @return CustomerGroupCollection
     */
    protected function getCustomerGroupCollection(): CustomerGroupCollection
    {
        return $this->customerGroupCollectionFactory->create();
    }

    /**
     * @return array
     */
    public function getCustomerGroups(): array
    {
        $customerGroupsCollection = $this->getCustomerGroupCollection();
        $customerGroups = $customerGroupsCollection->toOptionArray();

        return array_filter($customerGroups, static function ($group) {
            return (int)$group['value'] !== Group::NOT_LOGGED_IN_ID;
        });
    }
}

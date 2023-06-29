<?php
declare(strict_types=1);

namespace MacoOnboarding\CustomShippingModule\Provider;

use Magento\Customer\Model\Group;
use Magento\Customer\Model\ResourceModel\Group\Collection as CustomerGroupCollection;
use Magento\Customer\Model\ResourceModel\Group\CollectionFactory as CustomerGroupCollectionFactory;

class CustomerGroupsProvider
{
    private CustomerGroupCollectionFactory $customerGroupCollectionFactory;

    public function __construct(CustomerGroupCollectionFactory $customerGroupCollectionFactory)
    {
        $this->customerGroupCollectionFactory = $customerGroupCollectionFactory;
    }

    protected function getCustomerGroupCollection(): CustomerGroupCollection
    {
        return $this->customerGroupCollectionFactory->create();
    }

    public function getCustomerGroups()
    {
        $customerGroupsCollection = $this->getCustomerGroupCollection();
        $customerGroups = $customerGroupsCollection->toOptionArray();

        return array_filter($customerGroups, static function ($group) {
            return (int)$group['value'] !== Group::NOT_LOGGED_IN_ID;
        });
    }
}

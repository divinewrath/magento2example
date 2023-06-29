<?php
declare(strict_types=1);

namespace MacoOnboarding\CustomShippingModule\Model\Config\Backend;

use Magento\Config\Model\Config\Backend\Serialized\ArraySerialized;
use Magento\Framework\Exception\ValidatorException;

class ShipmentSerialized extends ArraySerialized
{
    public function beforeSave(): ShipmentSerialized
    {
        $array = $this->getValue();
        if (is_array($array)) {
            unset($array['__empty']);
            $this->checkDuplicates($array);
            $this->setValue($array);
        }

        return parent::beforeSave();
    }

    protected function checkDuplicates(array $customerGroupPrices): void
    {
        $customerGroups = array_column($customerGroupPrices, 'customer_groups');
        $customerGroupCounts = array_count_values($customerGroups);

        foreach ($customerGroupCounts as $customerGroup => $count) {
            if ($count > 1) {
                throw new ValidatorException(__('Duplicate group with ID: %1', $customerGroup));
            }
        }
    }
}

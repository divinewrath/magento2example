<?php

namespace MacoOnboarding\CustomShippingModule\Setup\Patch\Data;

use MacoOnboarding\CustomShippingModule\Constants;
use Magento\Customer\Api\CustomerMetadataInterface;
use Magento\Customer\Model\Customer;
use Magento\Customer\Model\ResourceModel\Attribute;
use Magento\Eav\Model\Config;
use Magento\Eav\Setup\EavSetup;
use Magento\Customer\Setup\CustomerSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Customer\Setup\CustomerSetupFactory;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;

class AddCustomShippingPriceCustomerAttribute implements DataPatchInterface
{
    private ModuleDataSetupInterface $moduleDataSetup;

    private CustomerSetupFactory $customerSetupFactory;

    private Config $eavConfig;

    private Attribute $attributeResource;

    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        CustomerSetupFactory     $customerSetupFactory,
        Config                   $eavConfig,
        Attribute                $attributeResource
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->customerSetupFactory = $customerSetupFactory;
        $this->eavConfig = $eavConfig;
        $this->attributeResource = $attributeResource;
    }

    public function apply(): self
    {
        /** @var CustomerSetup $customerSetup */
        $customerSetup = $this->customerSetupFactory->create(['setup' => $this->moduleDataSetup]);

        $customerSetup->addAttribute(
            Customer::ENTITY,
            Constants::ATTR_CUSTOM_SHIPPING_PRICE,
            [
                'type' => 'varchar',
                'label' => 'Custom shipping price',
                'input' => 'text',
                'required' => false,
                'visible' => true,
                'user_defined' => true,
                'system' => false,
                'position' => 200,
            ]
        );

        $customerSetup->addAttributeToSet(
            CustomerMetadataInterface::ENTITY_TYPE_CUSTOMER,
            CustomerMetadataInterface::ATTRIBUTE_SET_ID_CUSTOMER,
            null,
            Constants::ATTR_CUSTOM_SHIPPING_PRICE
        );

        $attribute = $this->eavConfig->getAttribute(Customer::ENTITY, Constants::ATTR_CUSTOM_SHIPPING_PRICE);

        $attribute->setData(
            'used_in_forms',
            ['adminhtml_customer']
        );
        $this->attributeResource->save($attribute);

        return $this;
    }

    public static function getDependencies(): array
    {
        return [];
    }

    public function getAliases(): array
    {
        return [];
    }
}

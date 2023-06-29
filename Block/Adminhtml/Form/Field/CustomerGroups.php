<?php
declare(strict_types=1);

namespace MacoOnboarding\CustomShippingModule\Block\Adminhtml\Form\Field;

use MacoOnboarding\CustomShippingModule\Provider\CustomerGroupsProvider;
use Magento\Framework\View\Element\Context;
use Magento\Framework\View\Element\Html\Select;

class CustomerGroups extends Select
{
    private CustomerGroupsProvider $customerGroupsProvider;

    public function __construct(
        Context                $context,
        CustomerGroupsProvider $customerGroupsProvider,
        array                  $data = []
    ) {
        parent::__construct($context, $data);

        $this->customerGroupsProvider = $customerGroupsProvider;
    }

    public function setInputName(string $value): self
    {
        return $this->setName($value);
    }

    public function setInputId(string $value): self
    {
        return $this->setId($value);
    }

    public function _toHtml(): string
    {
        if (!$this->getOptions()) {
            $this->setOptions($this->getSourceOptions());
        }

        return parent::_toHtml();
    }

    private function getSourceOptions(): array
    {
        return $this->getCustomerGroups();
    }

    private function getCustomerGroups(): array
    {
        return $this->customerGroupsProvider->getCustomerGroups();
    }
}

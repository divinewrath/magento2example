<?php
declare(strict_types=1);

namespace MacoOnboarding\CustomShippingModule\Block\Adminhtml\Form\Field;

use MacoOnboarding\CustomShippingModule\Provider\CustomerGroupsProvider;
use Magento\Framework\View\Element\Context;
use Magento\Framework\View\Element\Html\Select;

class CustomerGroups extends Select
{
    /**
     * @var CustomerGroupsProvider
     */
    private CustomerGroupsProvider $customerGroupsProvider;

    /**
     * @param Context $context
     * @param CustomerGroupsProvider $customerGroupsProvider
     * @param array $data
     */
    public function __construct(
        Context                $context,
        CustomerGroupsProvider $customerGroupsProvider,
        array                  $data = []
    ) {
        parent::__construct($context, $data);

        $this->customerGroupsProvider = $customerGroupsProvider;
    }

    /**
     * @param string $value
     * @return self
     */
    public function setInputName(string $value): self
    {
        return $this->setName($value);
    }

    /**
     * @param string $value
     * @return self
     */
    public function setInputId(string $value): self
    {
        return $this->setId($value);
    }

    /**
     * @return string
     */
    public function _toHtml(): string
    {
        if (!$this->getOptions()) {
            $this->setOptions($this->getSourceOptions());
        }

        return parent::_toHtml();
    }

    /**
     * @return array
     */
    private function getSourceOptions(): array
    {
        return $this->getCustomerGroups();
    }

    /**
     * @return array
     */
    private function getCustomerGroups(): array
    {
        return $this->customerGroupsProvider->getCustomerGroups();
    }
}

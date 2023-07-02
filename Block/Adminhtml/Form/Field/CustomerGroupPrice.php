<?php
declare(strict_types=1);

namespace MacoOnboarding\CustomShippingModule\Block\Adminhtml\Form\Field;

use Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\LocalizedException;

class CustomerGroupPrice extends AbstractFieldArray
{
    /**
     * @var CustomerGroups|null
     */
    private ?CustomerGroups $customerGroups = null;

    /**
     * @return void
     * @throws LocalizedException
     */
    protected function _prepareToRender(): void
    {
        $this->addColumn('customer_groups', [
            'label' => __('Customer groups'),
            'renderer' => $this->getCustomerGroupsRenderer()
        ]);
        $this->addColumn('price', ['label' => __('Price'), 'class' => 'required-entry validate-number']);

        $this->_addAfter = false;
        $this->_addButtonLabel = __('Add');
    }

    /**
     * @param DataObject $row
     * @throws LocalizedException
     */
    protected function _prepareArrayRow(DataObject $row): void
    {
        $options = [];

        $customerGroup = $row->getCustomerGroups();
        if ($customerGroup !== null) {
            $optionHash = $this->getCustomerGroupsRenderer()->calcOptionHash($customerGroup);
            $options['option_' . $optionHash] = 'selected="selected"';
        }

        $row->setData('option_extra_attrs', $options);
    }

    /**
     * @throws LocalizedException
     */
    protected function getCustomerGroupsRenderer(): CustomerGroups
    {
        if (!$this->customerGroups) {
            $this->customerGroups = $this->getLayout()->createBlock(
                CustomerGroups::class,
                '',
                ['data' => ['is_render_to_js_template' => true]]
            );
        }

        return $this->customerGroups;
    }
}

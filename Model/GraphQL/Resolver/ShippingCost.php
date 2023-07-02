<?php
declare(strict_types=1);

namespace MacoOnboarding\CustomShippingModule\Model\GraphQL\Resolver;

use MacoOnboarding\CustomShippingModule\Provider\CustomerDataProvider;
use MacoOnboarding\CustomShippingModule\Provider\ShippingCostProvider;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\GraphQl\Model\Query\Context;

class ShippingCost implements ResolverInterface
{
    /**
     * @var ShippingCostProvider
     */
    private ShippingCostProvider $shippingCostProvider;

    /**
     * @var CustomerDataProvider
     */
    private CustomerDataProvider $customerDataProvider;

    /**
     * @param ShippingCostProvider $shippingCostProvider
     * @param CustomerDataProvider $customerDataProvider
     */
    public function __construct(
        ShippingCostProvider $shippingCostProvider,
        CustomerDataProvider $customerDataProvider
    ) {
        $this->shippingCostProvider = $shippingCostProvider;
        $this->customerDataProvider = $customerDataProvider;
    }

    /**
     * @param Field $field
     * @param Context $context
     * @param ResolveInfo $info
     * @param array|null $value
     * @param array|null $args
     * @return array
     * @throws GraphQlInputException
     */
    public function resolve(Field $field, $context, ResolveInfo $info, array $value = null, array $args = null): array
    {
        if (!$context->getUserId()) {
            throw new GraphQlInputException(__('Customer must be logged'));
        }

        if (empty($args['email'])) {
            throw new GraphQlInputException(__('Email field is required'));
        }

        try {
            $customer = $this->customerDataProvider->getCustomerByEmail($args['email']);
            $cost = $this->shippingCostProvider->getShippingCost($customer);
            if ($cost) {
                return [
                    'cost' => $cost
                ];
            }
        } catch (LocalizedException $e) {
            throw new GraphQlInputException(__($e->getMessage()), $e);
        }

        throw new GraphQlInputException(__('Not found'));
    }
}

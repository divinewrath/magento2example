<?php
/**
 * @license http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author Łukasz Wojciechowski <contact@kaliop.com>
 * @copyright Copyright (c) 2023 Kaliop Digital Commerce (https://digitalcommerce.kaliop.com)
 */

declare(strict_types=1);

namespace MacoOnboarding\CustomShippingModule\Model\GraphQL\Resolver;

use MacoOnboarding\CustomShippingModule\Provider\CustomerDataProvider;
use MacoOnboarding\CustomShippingModule\Provider\ShippingCostProvider;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;

class ShippingCost implements ResolverInterface
{
    private ShippingCostProvider $shippingCostProvider;
    private CustomerDataProvider $customerDataProvider;

    public function __construct(
        ShippingCostProvider $shippingCostProvider,
        CustomerDataProvider $customerDataProvider
    ) {
        $this->shippingCostProvider = $shippingCostProvider;
        $this->customerDataProvider = $customerDataProvider;
    }

    public function resolve(Field $field, $context, ResolveInfo $info, array $value = null, array $args = null)
    {
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

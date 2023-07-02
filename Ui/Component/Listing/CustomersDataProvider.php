<?php
declare(strict_types=1);
namespace MacoOnboarding\CustomShippingModule\Ui\Component\Listing;

use MacoOnboarding\CustomShippingModule\Provider\ShippingPricesConfigProvider;
use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\Search\ReportingInterface;
use Magento\Framework\Api\Search\SearchCriteriaBuilder;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\View\Element\UiComponent\DataProvider\DataProvider;

class CustomersDataProvider extends DataProvider
{
    private ShippingPricesConfigProvider $shippingPricesConfigProvider;

    private array $shippingPrices = [];

    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        ReportingInterface $reporting,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        RequestInterface $request,
        FilterBuilder $filterBuilder,
        ShippingPricesConfigProvider $shippingPricesConfigProvider,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct(
            $name,
            $primaryFieldName,
            $requestFieldName,
            $reporting,
            $searchCriteriaBuilder,
            $request,
            $filterBuilder,
            $meta,
            $data
        );

        $this->shippingPricesConfigProvider = $shippingPricesConfigProvider;
    }

    public function getData(): array
    {
        $customers = parent::getData();
        $groupPrices = $this->getShippingPrices();

        foreach ($customers['items'] as &$customer) {
            if ($customer['custom_shipping_price'] === null) {
                if (array_key_exists($customer['group_id'], $groupPrices) && $groupPrices[$customer['group_id']]) {
                    $customer['custom_shipping_price'] = $groupPrices[$customer['group_id']];
                }
            }
        }

        return $customers;
    }

    protected function getShippingPrices(): array
    {
        if (!$this->shippingPrices) {
            $this->shippingPrices = $this->shippingPricesConfigProvider->getGroupPrices();
        }

        return $this->shippingPrices;
    }
}

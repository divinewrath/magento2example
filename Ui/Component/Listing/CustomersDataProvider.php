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
    /**
     * @var ShippingPricesConfigProvider
     */
    private ShippingPricesConfigProvider $shippingPricesConfigProvider;

    /**
     * @var array
     */
    private array $shippingPrices = [];

    /**
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param ReportingInterface $reporting
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param RequestInterface $request
     * @param FilterBuilder $filterBuilder
     * @param ShippingPricesConfigProvider $shippingPricesConfigProvider
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        string $name,
        string $primaryFieldName,
        string $requestFieldName,
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

    /**
     * @return array
     */
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

    /**
     * @return array
     */
    protected function getShippingPrices(): array
    {
        if (!$this->shippingPrices) {
            $this->shippingPrices = $this->shippingPricesConfigProvider->getGroupPrices();
        }

        return $this->shippingPrices;
    }
}

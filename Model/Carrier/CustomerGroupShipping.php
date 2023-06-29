<?php
declare(strict_types=1);

namespace MacoOnboarding\CustomShippingModule\Model\Carrier;

use MacoOnboarding\CustomShippingModule\Constants;
use MacoOnboarding\CustomShippingModule\Provider\ShippingPricesProvider;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Model\Group;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Quote\Model\Quote\Address\RateRequest;
use Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory;
use Magento\Quote\Model\Quote\Address\RateResult\MethodFactory;
use Magento\Shipping\Model\Carrier\AbstractCarrier;
use Magento\Shipping\Model\Carrier\CarrierInterface;
use Magento\Shipping\Model\Rate\Result;
use Magento\Shipping\Model\Rate\ResultFactory;
use Psr\Log\LoggerInterface;

class CustomerGroupShipping extends AbstractCarrier implements CarrierInterface
{

    protected $_code = 'customergroupshipping';

    protected $_isFixed = true;

    protected ResultFactory $rateResultFactory;

    protected MethodFactory $rateMethodFactory;
    private CustomerSession $customerSession;
    private CustomerRepositoryInterface $customerRepository;
    private ShippingPricesProvider $shippingPricesProvider;

    public function __construct(
        ScopeConfigInterface        $scopeConfig,
        ErrorFactory                $rateErrorFactory,
        LoggerInterface             $logger,
        ResultFactory               $rateResultFactory,
        MethodFactory               $rateMethodFactory,
        CustomerSession             $customerSession,
        CustomerRepositoryInterface $customerRepository,
        ShippingPricesProvider      $shippingPricesProvider,
        array                       $data = []
    )
    {
        $this->rateResultFactory = $rateResultFactory;
        $this->rateMethodFactory = $rateMethodFactory;
        $this->customerSession = $customerSession;
        $this->customerRepository = $customerRepository;
        $this->shippingPricesProvider = $shippingPricesProvider;

        parent::__construct($scopeConfig, $rateErrorFactory, $logger, $data);
    }

    public
    function collectRates(RateRequest $request): Result|bool
    {
        if (!$this->getConfigFlag('active')) {
            return false;
        }

        $shippingPrice = $this->getConfigData('price');

        if ($this->customerSession->isLoggedIn()) {
            $groupPrice = $this->getCustomerGroupPrice(
                (int)$this->customerSession->getCustomerId()
            );

            if ($groupPrice) {
                $shippingPrice = $groupPrice;
            }
        }

        $result = $this->rateResultFactory->create();

        if ($shippingPrice !== false) {
            $method = $this->rateMethodFactory->create();

            $method->setCarrier($this->_code);
            $method->setCarrierTitle($this->getConfigData('title'));

            $method->setMethod($this->_code);
            $method->setMethodTitle($this->getConfigData('name'));

            if ($request->getFreeShipping() === true) {
                $shippingPrice = '0.00';
            }

            $method->setPrice($shippingPrice);
            $method->setCost($shippingPrice);

            $result->append($method);
        }

        return $result;
    }

    public
    function getAllowedMethods(): array
    {
        return [
            $this->_code => $this->getConfigData('name')
        ];
    }

    protected
    function getCustomerById(int $customerId): CustomerInterface
    {
        return $this->customerRepository->getById($customerId);
    }

    protected
    function getCustomerGroupPrice(int $customerId)
    {
        $customer = $this->getCustomerById($customerId);
        $groupId = $customer->getGroupId();
        $groupPrices = $this->shippingPricesProvider->getGroupPrices();

        return $groupPrices[$groupId] ?? null;
    }
}

<?php
declare(strict_types=1);

namespace MacoOnboarding\CustomShippingModule\Model\Carrier;

use MacoOnboarding\CustomShippingModule\Provider\ShippingCostProvider;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
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

    /**
     * @var string
     */
    protected $_code = 'customergroupshipping';

    /**
     * @var bool
     */
    protected $_isFixed = true;

    /**
     * @var ResultFactory
     */
    protected ResultFactory $rateResultFactory;

    /**
     * @var MethodFactory
     */
    protected MethodFactory $rateMethodFactory;

    /**
     * @var CustomerSession
     */
    private CustomerSession $customerSession;

    /**
     * @var CustomerRepositoryInterface
     */
    private CustomerRepositoryInterface $customerRepository;

    /**
     * @var ShippingCostProvider
     */
    private ShippingCostProvider $shippingCostProvider;

    /**
     * @param ScopeConfigInterface $scopeConfig
     * @param ErrorFactory $rateErrorFactory
     * @param LoggerInterface $logger
     * @param ResultFactory $rateResultFactory
     * @param MethodFactory $rateMethodFactory
     * @param CustomerSession $customerSession
     * @param CustomerRepositoryInterface $customerRepository
     * @param ShippingCostProvider $shippingCostProvider
     * @param array $data
     */
    public function __construct(
        ScopeConfigInterface        $scopeConfig,
        ErrorFactory                $rateErrorFactory,
        LoggerInterface             $logger,
        ResultFactory               $rateResultFactory,
        MethodFactory               $rateMethodFactory,
        CustomerSession             $customerSession,
        CustomerRepositoryInterface $customerRepository,
        ShippingCostProvider        $shippingCostProvider,
        array                       $data = []
    ) {
        $this->rateResultFactory = $rateResultFactory;
        $this->rateMethodFactory = $rateMethodFactory;
        $this->customerSession = $customerSession;
        $this->customerRepository = $customerRepository;
        $this->shippingCostProvider = $shippingCostProvider;

        parent::__construct($scopeConfig, $rateErrorFactory, $logger, $data);
    }

    /**
     * @param RateRequest $request
     * @return Result|bool
     */
    public function collectRates(RateRequest $request): Result|bool
    {
        if (!$this->getConfigFlag('active')) {
            return false;
        }

        $shippingPrice = $this->getConfigData('price');

        if ($this->customerSession->isLoggedIn()) {
            $customerShippingPrice = $this->getShippingPrice(
                (int)$this->customerSession->getCustomerId()
            );

            if ($customerShippingPrice) {
                $shippingPrice = $customerShippingPrice;
            }
        }

        $result = $this->rateResultFactory->create();

        if ($shippingPrice) {
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

    /**
     * @param int $customerId
     * @return mixed|null
     */
    protected function getShippingPrice(int $customerId)
    {
        $customer = $this->getCustomerById($customerId);

        return $this->shippingCostProvider->getShippingCost($customer);
    }

    /**
     * @param int $customerId
     * @return CustomerInterface
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    protected function getCustomerById(int $customerId): CustomerInterface
    {
        return $this->customerRepository->getById($customerId);
    }

    /**
     * @return array
     */
    public function getAllowedMethods(): array
    {
        return [
            $this->_code => $this->getConfigData('name')
        ];
    }
}

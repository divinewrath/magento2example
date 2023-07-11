<?php
declare(strict_types=1);

namespace MacoOnboarding\CustomShippingModule\Controller\Adminhtml\Customers;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\NotFoundException;
use Magento\Framework\View\Result\PageFactory;

class ListAction extends Action implements HttpGetActionInterface
{
    public const ADMIN_RESOURCE = 'MacoOnboarding_CustomShippingModule::listing';
    /**
     * @var PageFactory
     */
    private PageFactory $pageFactory;

    /**
     * @param Context $context
     * @param PageFactory $pageFactory
     */
    public function __construct(
        Context $context,
        PageFactory $pageFactory
    ) {
        parent::__construct($context);

        $this->pageFactory = $pageFactory;
    }

    /**
     * @return ResultInterface
     */
    public function execute()
    {
        $resultPage = $this->pageFactory->create();
        $resultPage->getConfig()->getTitle()->set(__('Customers shipping cost'));

        return $resultPage;
    }
}

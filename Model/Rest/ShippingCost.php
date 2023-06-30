<?php
declare(strict_types=1);

namespace MacoOnboarding\CustomShippingModule\Model\Rest;

use MacoOnboarding\CustomShippingModule\Api\Data\ShippingCostInterface;
use Magento\Framework\Model\AbstractModel;

class ShippingCost extends AbstractModel implements ShippingCostInterface
{
    public function getShippingCost()
    {
        return $this->getData(self::SHIPPING_COST);
    }

    public function setShippingCost(string $shippingCost): self
    {
        return $this->setData(self::SHIPPING_COST, $shippingCost);
    }
}

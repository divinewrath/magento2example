<?php
namespace MacoOnboarding\CustomShippingModule\Api\Data;

interface ShippingCostInterface
{
    public const SHIPPING_COST = 'shipping_cost';

    /**
     * @return string
     */
    public function getShippingCost();

    /**
     * @param string $shippingCost
     * @return self
     */
    public function setShippingCost(string $shippingCost);
}

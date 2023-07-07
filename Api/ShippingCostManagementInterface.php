<?php
namespace MacoOnboarding\CustomShippingModule\Api;

use MacoOnboarding\CustomShippingModule\Api\Data\ShippingCostInterface;

interface ShippingCostManagementInterface
{
    /**
     * @param string $email
     * @param int $customerId
     * @return \MacoOnboarding\CustomShippingModule\Api\Data\ShippingCostInterface
     */
    public function getShippingCost(string $email, int $customerId): ShippingCostInterface;
}

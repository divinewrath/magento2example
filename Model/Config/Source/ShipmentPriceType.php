<?php
declare(strict_types=1);

namespace MacoOnboarding\CustomShippingModule\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

class ShipmentPriceType implements OptionSourceInterface
{
    public const PRICE_TYPE_FOR_ITEM = 'for_item';
    public const PRICE_TYPE_FOR_SHIPMENT = 'for_shipment';

    public function toOptionArray(): array
    {
        return [
            ['label' => 'for item', 'value' => self::PRICE_TYPE_FOR_ITEM],
            ['label' => 'for shipment', 'value' => self::PRICE_TYPE_FOR_SHIPMENT],
        ];
    }
}

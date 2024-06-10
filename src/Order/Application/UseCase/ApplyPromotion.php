<?php

namespace App\Order\Application\UseCase;

use App\Order\Domain\Model\OrderItem;
use App\Promotion\Domain\Model\Promotion;

class ApplyPromotion
{
    public function execute(Promotion $promotion, OrderItem $orderItem): void
    {
        // Promotions type 1 always will be overwritten
        if ($promotion->getType() == Promotion::TYPE_ITEM && in_array($orderItem->getProduct()->getType(), $promotion->getProductTypesFilter())) {
            $orderItem->setDiscount($promotion->getPercentageDiscount());
            $discountValue = (int) round($orderItem->getUnitPrice() * $promotion->getPercentageDiscount() / 100);
            $orderItem->setDiscountValue($discountValue);
            $orderItem->setDiscountedUnitPrice($orderItem->getUnitPrice() - $discountValue);
        }

        if ($promotion->getType() == Promotion::TYPE_ORDER) {
            $discountedUnitPrice = $orderItem->getDiscountedUnitPrice() ? : $orderItem->getUnitPrice();
            $discountValue = (int) round($discountedUnitPrice * $promotion->getPercentageDiscount() / 100);
            $orderItem->setDistributedOrderDiscountValue($discountValue);
            $orderItem->setDiscountedUnitPrice($discountedUnitPrice - $discountValue);
        }
    }

}
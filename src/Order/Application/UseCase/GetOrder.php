<?php

namespace App\Order\Application\UseCase;

use App\Order\Adapter\Doctrine\OrderItemRepository;
use App\Order\Adapter\Doctrine\OrderRepository;
use App\Order\Domain\Model\OrderItem;
use App\Promotion\Domain\Model\Promotion;
use Doctrine\Common\Collections\Criteria;

class GetOrder
{
    private OrderRepository $orderRepository;
    private OrderItemRepository $orderItemRepository;
    private ApplyPromotion $applyPromotion;
    private TaxRateCalculate $taxRateCalculate;

    public function __construct(
        OrderRepository $orderRepository,
        OrderItemRepository $orderItemRepository,
        ApplyPromotion $applyPromotion,
        TaxRateCalculate $taxRateCalculate)
    {
        $this->orderRepository = $orderRepository;
        $this->orderItemRepository = $orderItemRepository;
        $this->applyPromotion = $applyPromotion;
        $this->taxRateCalculate = $taxRateCalculate;
    }

    /**
     * @throws \Exception
     */
    public function execute(int $orderId): array
    {
        $order = $this->orderRepository->findById($orderId);
        $criteria = Criteria::create()->orderBy(array("type" => Criteria::ASC)); // for sorting by time need to create a new entity and add field created_at
        $promotions = $order->getPromotions()->matching($criteria);
        $orderItems = $order->getItems();
        $total = 0;
        $taxTotal = 0;
        $adjustmentsTotal = 0;
        $items = [];

        /** @var OrderItem $orderItem */
        foreach ($orderItems as $orderItem) {
            /** @var Promotion $promotion */
            foreach ($promotions as $promotion) {
                $this->applyPromotion->execute($promotion, $orderItem);
            }
            $discountedUnitPrice = $orderItem->getDiscountedUnitPrice() ? : $orderItem->getUnitPrice();
            $orderItemTotal = $discountedUnitPrice * $orderItem->getQuantity();
            $orderItem->setTotal($orderItemTotal);
            $orderItemTax = $this->taxRateCalculate->execute($orderItem->getProduct(), $orderItemTotal);
            $orderItem->setTaxValue($orderItemTax);

            $adjustmentsTotal = $adjustmentsTotal - $orderItem->getDiscountValue() - $orderItem->getDistributedOrderDiscountValue();
            $total =+ $orderItemTotal;
            $taxTotal =+ $orderItemTax;

            $this->orderItemRepository->save($orderItem);

            $items[] = [
                "id" => $orderItem->getId(),
                "product" => [
                  "code" => $orderItem->getProduct()->getCode(),
                  "name" => $orderItem->getProduct()->getName()
                ],
                "unitPrice" => $orderItem->getUnitPrice(),
                "discount" => $orderItem->getDiscount(),
                "discountValue" => $orderItem->getDiscountValue(),
                "distributedOrderDiscountValue" => $orderItem->getDistributedOrderDiscountValue(),
                "discountedUnitPrice" => $orderItem->getDiscountedUnitPrice(),
                "quantity" => $orderItem->getQuantity(),
                "total" => $orderItem->getTotal(),
                "taxValue" => $orderItem->getTaxValue(),
            ];
        }
        $order->setTotal($total);
        $order->setAdjustmentsTotal($adjustmentsTotal);
        $this->orderRepository->save($order);

        return [
            "id" => $order->getId(),
            "itemsTotal" => $order->getItemsTotal(),
            "taxTotal" => $taxTotal,
            "total" => $total,
            "items" => $items
        ];
    }
}

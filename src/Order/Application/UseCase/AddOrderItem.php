<?php

namespace App\Order\Application\UseCase;


use App\Order\Adapter\Doctrine\OrderItemRepository;
use App\Order\Adapter\Doctrine\OrderRepository;
use App\Order\Domain\Model\Order;
use App\Order\Domain\Model\OrderItem;
use App\Product\Adapter\Doctrine\ProductRepository;
use App\UseCase\TaxRateCalculate;

class AddOrderItem
{
    private OrderItemRepository $orderItemRepository;
    private OrderRepository $orderRepository;
    private ProductRepository $productRepository;
    private TaxRateCalculate $taxRateCalculate;

    public function __construct(
        OrderItemRepository $orderItemRepository,
        OrderRepository $orderRepository,
        ProductRepository $productRepository,
        TaxRateCalculate $taxRateCalculate
    )
    {
        $this->orderItemRepository = $orderItemRepository;
        $this->orderRepository = $orderRepository;
        $this->productRepository = $productRepository;
        $this->taxRateCalculate = $taxRateCalculate;
    }

    /**
     * @throws \Exception
     */
    public function execute(int $orderId, int $productId): void
    {
        $order = $this->orderRepository->findById($orderId);
        $product = $this->productRepository->findById($productId);
        $orderItem = $this->orderItemRepository->findByOrderAndProduct($order, $product);
        if (!$orderItem) {
            if ($order->getItemsTotal() == Order::MAX_ITEMS_PER_ORDER) {
                throw new \Exception("Order couldn't have more than " . Order::MAX_ITEMS_PER_ORDER . " different items");
            }
            $orderItem = new OrderItem();
            $orderItem->setOrder($order);
            $orderItem->setProduct($product);
            $orderItem->setUnitPrice($product->getPrice()); // What if product has already added, but price in Product changed? Can be resolve if Product is unchangeable entity.
        }
        $quantity = $orderItem->getQuantity() + 1;
        if ($quantity > OrderItem::MAX_QUANTITY) {
            throw new \Exception("Quantity couldn't be more than " . OrderItem::MAX_QUANTITY);
        }
        $orderItem->setQuantity($quantity);
        $orderItem->setTaxValue($this->taxRateCalculate->execute($product, $orderItem->getSubtotal()));
        $orderItem->setTotal($orderItem->getSubtotal());
        $this->orderItemRepository->save($orderItem);
    }
}

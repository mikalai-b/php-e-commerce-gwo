<?php

namespace App\Tests\PhpUnit\App\Order\Entity;

use App\Order\Domain\Model\Order;
use App\Order\Domain\Model\OrderItem;
use PHPUnit\Framework\TestCase;

/**
 * For example purposes only (can be removed)
 */
class OrderTest extends TestCase
{
    public function testOrderTotalCalculation(): void
    {
        $orderItem = new OrderItem();
        $orderItem->setQuantity(2);
        $orderItem->setUnitPrice(99);
        $orderItem->recalculateTotal();

        $order = new Order();
        $order->addItem($orderItem);

        $this->assertSame(198, $order->getItemsTotal());
    }
}

<?php

namespace App\Tests\PhpUnit\Order\Application\UseCase;

use App\Order\Application\UseCase\ApplyPromotion;
use App\Order\Application\UseCase\TaxRateCalculate;
use App\Product\Domain\Model\Product;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use App\Order\Application\UseCase\GetOrder;
use App\Order\Adapter\Doctrine\OrderRepository;
use App\Order\Adapter\Doctrine\OrderItemRepository;
use App\Order\Domain\Model\Order;
use App\Order\Domain\Model\OrderItem;
use App\Promotion\Domain\Model\Promotion;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;

class GetOrderTest extends TestCase
{
    private OrderRepository&MockObject $orderRepository;
    private OrderItemRepository&MockObject $orderItemRepository;
    private ApplyPromotion&MockObject $applyPromotion;
    private TaxRateCalculate&MockObject $taxRateCalculate;
    private GetOrder $getOrderUseCase;

    protected function setUp(): void
    {
        $this->orderRepository = $this->createMock(OrderRepository::class);
        $this->orderItemRepository = $this->createMock(OrderItemRepository::class);
        $this->applyPromotion = $this->createMock(ApplyPromotion::class);
        $this->taxRateCalculate = $this->createMock(TaxRateCalculate::class);
        $this->getOrderUseCase = new GetOrder($this->orderRepository, $this->orderItemRepository, $this->applyPromotion, $this->taxRateCalculate);
    }

    /**
     * @throws \Exception
     */
    public function testExecute(): void
    {
        $orderId = 1;
        $order = $this->createMock(Order::class);
        $orderItem1 = $this->createMock(OrderItem::class);
        $orderItem2 = $this->createMock(OrderItem::class);
        $promotion = $this->createMock(Promotion::class);

        $orderItems = new ArrayCollection([$orderItem1, $orderItem2]);
        $promotions = new ArrayCollection([$promotion]);

        $order->method('getPromotions')->willReturn($promotions);
        $order->method('getItems')->willReturn($orderItems);

        $this->orderRepository->method('findById')->willReturn($order);

        $orderItem1->method('getDiscountedUnitPrice')->willReturn(0);
        $orderItem1->method('getUnitPrice')->willReturn(100);
        $orderItem1->method('getQuantity')->willReturn(2);
        $orderItem1->method('getProduct')->willReturn($this->createMock(Product::class));

        $orderItem2->method('getDiscountedUnitPrice')->willReturn(0);
        $orderItem2->method('getUnitPrice')->willReturn(50);
        $orderItem2->method('getQuantity')->willReturn(3);
        $orderItem2->method('getProduct')->willReturn($this->createMock(Product::class));

        $this->applyPromotion->expects($this->exactly(2))->method('execute');

        $this->taxRateCalculate->method('execute')->willReturn(10);

        $orderItem1->expects($this->once())->method('setTotal')->with(200);
        $orderItem1->expects($this->once())->method('setTaxValue')->with(10);
        $orderItem2->expects($this->once())->method('setTotal')->with(150);
        $orderItem2->expects($this->once())->method('setTaxValue')->with(10);

        $this->orderItemRepository->expects($this->exactly(2))->method('save');

        $order->expects($this->once())->method('setTotal')->with(350);
        $order->expects($this->once())->method('setAdjustmentsTotal');
        $this->orderRepository->expects($this->once())->method('save');

        $result = $this->getOrderUseCase->execute($orderId);

        $this->assertIsArray($result);
        $this->assertEquals(350, $result['total']);
        $this->assertEquals(20, $result['taxTotal']);
    }
}

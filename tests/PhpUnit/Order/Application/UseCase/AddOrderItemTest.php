<?php

namespace App\Tests\PhpUnit\Order\Application\UseCase;


use App\Order\Adapter\Doctrine\OrderItemRepository;
use App\Order\Adapter\Doctrine\OrderRepository;
use App\Order\Application\UseCase\AddOrderItem;
use App\Order\Application\UseCase\TaxRateCalculate;
use App\Order\Domain\Model\Order;
use App\Order\Domain\Model\OrderItem;
use App\Product\Adapter\Doctrine\ProductRepository;
use App\Product\Domain\Model\Product;
use Exception;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class AddOrderItemTest extends TestCase
{
    private OrderItemRepository $orderItemRepository;
    private OrderRepository $orderRepository;
    private ProductRepository $productRepository;
    private TaxRateCalculate $taxRateCalculate;
    private AddOrderItem $case;

    public function setUp(): void
    {
        $this->orderItemRepository = $this->createMock(OrderItemRepository::class);
        $this->orderRepository = $this->createMock(OrderRepository::class);
        $this->productRepository = $this->createMock(ProductRepository::class);
        $this->taxRateCalculate = $this->createMock(TaxRateCalculate::class);

        $this->case = new AddOrderItem(
            $this->orderItemRepository,
            $this->orderRepository,
            $this->productRepository,
            $this->taxRateCalculate
        );
    }

    /**
     * @throws Exception
     */
    public function testExecuteOrderNotFound(): void
    {
        $this->orderRepository
            ->expects($this->once())
            ->method('findById')
            ->willThrowException(new NotFoundHttpException('Order not found'));

        $this->expectException(NotFoundHttpException::class);
        $this->expectExceptionMessage('Order not found');

        $this->case->execute(1, 1);
    }

    /**
     * @throws Exception
     */
    public function testExecuteProductNotFound(): void
    {
        $order = new Order();

        $this->orderRepository
            ->expects($this->once())
            ->method('findById')
            ->willReturn($order);

        $this->productRepository
            ->expects($this->once())
            ->method('findById')
            ->willThrowException(new NotFoundHttpException('Product not found'));

        $this->expectException(NotFoundHttpException::class);
        $this->expectExceptionMessage('Product not found');

        $this->case->execute(1, 1);
    }

    public function testExecuteMaxItemsPerOrderExceeded(): void
    {
        $order = $this->createMock(Order::class);
        $order->expects($this->once())->method('getItemsTotal')->willReturn(Order::MAX_ITEMS_PER_ORDER);
        $product = new Product();

        $this->orderRepository->expects($this->once())->method('findById')->willReturn($order);
        $this->productRepository->expects($this->once())->method('findById')->willReturn($product);
        $this->orderItemRepository
            ->expects($this->once())
            ->method('findByOrderAndProduct')
            ->willReturn(null);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage("Order couldn't have more than " . Order::MAX_ITEMS_PER_ORDER . " different items");

        $this->case->execute(1, 1);
    }

    public function testExecuteMaxQuantityExceeded(): void
    {
        $order = new Order();
        $product = new Product();
        $orderItem = $this->createMock(OrderItem::class);
        $orderItem->expects($this->once())->method('getQuantity')->willReturn(OrderItem::MAX_QUANTITY);

        $this->orderRepository->method('findById')->willReturn($order);
        $this->productRepository->method('findById')->willReturn($product);
        $this->orderItemRepository->method('findByOrderAndProduct')->willReturn($orderItem);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage("Quantity couldn't be more than " . OrderItem::MAX_QUANTITY);

        $this->case->execute(1, 1);
    }

    /**
     * @throws Exception
     */
    public function testExecuteSuccess(): void
    {
        $order = $this->createMock(Order::class);
        $order->expects($this->once())->method("getItemsTotal")->willReturn(0);
        $product = $this->createMock(Product::class);
        $orderItem = $this->createMock(OrderItem::class);
        $orderItem->expects($this->once())->method('getQuantity')->willReturn(1);
        $orderItem->expects($this->exactly(2))->method('getSubtotal')->willReturn(100);

        $this->orderRepository->expects($this->once())->method('findById')->willReturn($order);
        $this->productRepository->expects($this->once())->method('findById')->willReturn($product);
        $this->orderItemRepository
            ->expects($this->once())
            ->method('findByOrderAndProduct')
            ->willReturn($orderItem);

        $this->taxRateCalculate->expects($this->once())->method('execute')->willReturn(20);

        $orderItem->expects($this->once())->method('setQuantity')->with(2);
        $orderItem->expects($this->once())->method('setTaxValue')->with(20);
        $orderItem->expects($this->once())->method('setTotal')->with(100);

        $this->orderItemRepository->expects($this->once())->method('save')->with($orderItem);
        $order->expects($this->once())->method("setItemsTotal")->with(2);

        $this->orderRepository->expects($this->once())->method('save');

        $this->case->execute(1, 1);
    }
}

<?php

namespace App\Tests\PhpUnit\Order\Application\UseCase;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use App\Order\Application\UseCase\ApplyPromotion;
use App\Order\Domain\Model\OrderItem;
use App\Promotion\Domain\Model\Promotion;
use App\Product\Domain\Model\Product;

class ApplyPromotionTest extends TestCase
{
    private Promotion&MockObject $promotion;
    private OrderItem&MockObject $orderItem;
    private ApplyPromotion $applyPromotionUseCase;

    protected function setUp(): void
    {
        $this->promotion = $this->createMock(Promotion::class);
        $this->orderItem = $this->createMock(OrderItem::class);
        $this->applyPromotionUseCase = new ApplyPromotion();
    }

    public function testExecuteAppliesItemPromotion(): void
    {
        $product = $this->createMock(Product::class);

        $this->promotion->method('getType')->willReturn(Promotion::TYPE_ITEM);
        $this->promotion->method('getProductTypesFilter')->willReturn([Product::TYPE_BOOK]);
        $this->promotion->method('getPercentageDiscount')->willReturn(10);

        $product->method('getType')->willReturn(Product::TYPE_BOOK);
        $this->orderItem->method('getProduct')->willReturn($product);
        $this->orderItem->method('getUnitPrice')->willReturn(100);

        $this->orderItem->expects($this->once())->method('setDiscount')->with(10);
        $this->orderItem->expects($this->once())->method('setDiscountValue')->with(10);
        $this->orderItem->expects($this->once())->method('setDiscountedUnitPrice')->with(90);

        $this->applyPromotionUseCase->execute($this->promotion, $this->orderItem);
    }

    public function testExecuteAppliesOrderPromotion(): void
    {
        $this->promotion->method('getType')->willReturn(Promotion::TYPE_ORDER);
        $this->promotion->method('getPercentageDiscount')->willReturn(20);

        $this->orderItem->method('getUnitPrice')->willReturn(100);
        $this->orderItem->method('getDiscountedUnitPrice')->willReturn(0);

        $this->orderItem->expects($this->once())->method('setDistributedOrderDiscountValue')->with(20);
        $this->orderItem->expects($this->once())->method('setDiscountedUnitPrice')->with(80);

        $this->applyPromotionUseCase->execute($this->promotion, $this->orderItem);
    }

    public function testExecuteAppliesOrderPromotionOnDiscountedItem(): void
    {
        $this->promotion->method('getType')->willReturn(Promotion::TYPE_ORDER);
        $this->promotion->method('getPercentageDiscount')->willReturn(20);

        $this->orderItem->method('getUnitPrice')->willReturn(100);
        $this->orderItem->method('getDiscountedUnitPrice')->willReturn(90);

        $this->orderItem->expects($this->once())->method('setDistributedOrderDiscountValue')->with(18);
        $this->orderItem->expects($this->once())->method('setDiscountedUnitPrice')->with(72);

        $this->applyPromotionUseCase->execute($this->promotion, $this->orderItem);
    }

    public function testExecuteDoesNotApplyItemPromotionWhenProductTypeMismatch(): void
    {
        $product = $this->createMock(Product::class);

        $this->promotion->method('getType')->willReturn(Promotion::TYPE_ITEM);
        $this->promotion->method('getProductTypesFilter')->willReturn(['type2']);

        $this->orderItem->method('getProduct')->willReturn($product);
        $product->method('getType')->willReturn('type1');

        $this->orderItem->expects($this->never())->method('setDiscount');
        $this->orderItem->expects($this->never())->method('setDiscountValue');
        $this->orderItem->expects($this->never())->method('setDiscountedUnitPrice');

        $this->applyPromotionUseCase->execute($this->promotion, $this->orderItem);
    }
}

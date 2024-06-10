<?php

namespace App\Tests\PhpUnit\Order\Application\UseCase;

use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use App\Order\Application\UseCase\AddPromotion;
use App\Order\Adapter\Doctrine\OrderRepository;
use App\Promotion\Adapter\Doctrine\PromotionRepository;
use App\Order\Domain\Model\Order;
use App\Promotion\Domain\Model\Promotion;

class AddPromotionTest extends TestCase
{
    private OrderRepository&MockObject $orderRepository;
    private PromotionRepository&MockObject $promotionRepository;
    private AddPromotion $addPromotionUseCase;

    protected function setUp(): void
    {
        $this->orderRepository = $this->createMock(OrderRepository::class);
        $this->promotionRepository = $this->createMock(PromotionRepository::class);
        $this->addPromotionUseCase = new AddPromotion($this->orderRepository, $this->promotionRepository);
    }

    public function testExecuteThrowsExceptionIfPromotionAlreadyAdded(): void
    {
        $order = $this->createMock(Order::class);
        $promo = $this->createMock(Promotion::class);

        $order->expects($this->once())->method('hasPromotion')->with($promo)->willReturn(true);

        $this->orderRepository->expects($this->once())->method('findById')->willReturn($order);
        $this->promotionRepository->expects($this->once())->method('findById')->willReturn($promo);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('One promotion can be added only once');

        $this->addPromotionUseCase->execute(1, 1);
    }

    public function testExecuteThrowsExceptionIfMultipleOrderTypePromotionsAdded(): void
    {
        $order = $this->createMock(Order::class);
        $promo = $this->createMock(Promotion::class);

        $promo->expects($this->once())->method('getType')->willReturn(Promotion::TYPE_ORDER);

        $existingPromo = $this->createMock(Promotion::class);
        $existingPromo->method('getType')->willReturn(Promotion::TYPE_ORDER);

        $promotions = new ArrayCollection([$existingPromo]);

        $order->expects($this->once())->method('getPromotions')->willReturn($promotions);

        $this->orderRepository->expects($this->once())->method('findById')->willReturn($order);
        $this->promotionRepository->expects($this->once())->method('findById')->willReturn($promo);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Only one promotion type ' . Promotion::TYPE_ORDER . ' can be added');

        $this->addPromotionUseCase->execute(1, 1);
    }

    public function testExecuteAddsPromotionSuccessfully(): void
    {
        $order = $this->createMock(Order::class);
        $promo = $this->createMock(Promotion::class);

        $promo->expects($this->once())->method('getType')->willReturn(Promotion::TYPE_ORDER);
        $order->expects($this->once())->method('hasPromotion')->with($promo)->willReturn(false);

        $promotions = new ArrayCollection([]);

        $order->expects($this->once())->method('getPromotions')->willReturn($promotions);
        $order->expects($this->once())->method('addPromotion')->with($promo);

        $this->orderRepository->expects($this->once())->method('findById')->willReturn($order);
        $this->promotionRepository->expects($this->once())->method('findById')->willReturn($promo);
        $this->orderRepository->expects($this->once())->method('save')->with($order);

        $this->addPromotionUseCase->execute(1, 1);
    }
}

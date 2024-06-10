<?php

namespace App\Order\Application\UseCase;

use App\Order\Adapter\Doctrine\OrderRepository;
use App\Promotion\Adapter\Doctrine\PromotionRepository;
use App\Promotion\Domain\Model\Promotion;

class AddPromotion
{
    private OrderRepository $orderRepository;
    private PromotionRepository $promotionRepository;

    public function __construct(OrderRepository $orderRepository, PromotionRepository $promotionRepository)
    {
        $this->orderRepository = $orderRepository;
        $this->promotionRepository = $promotionRepository;
    }

    /**
     * @throws \Exception
     */
    public function execute(int $orderId, int $promoId): void
    {
        $order = $this->orderRepository->findById($orderId);
        $promo = $this->promotionRepository->findById($promoId);

        if ($order->hasPromotion($promo)) {
            throw new \Exception("One promotion can be added only once");
        }

        if ($promo->getType() == Promotion::TYPE_ORDER &&
            count($order->getPromotions()->filter(function (Promotion $promotion) {
            return Promotion::TYPE_ORDER == $promotion->getType();
        }))) {
            throw new \Exception("Only one promotion type " . Promotion::TYPE_ORDER . " can be added");
        }

        $order->addPromotion($promo);
        $this->orderRepository->save($order);
    }
}

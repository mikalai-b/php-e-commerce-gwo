<?php

namespace App\Order\Application\UseCase;

use App\Order\Adapter\Doctrine\OrderRepository;
use App\Order\Domain\Model\Order;
use App\User\Adapter\Doctrine\UserRepository;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CreateOrder
{
    private OrderRepository $orderRepository;
    private UserRepository $userRepository;

    public function __construct(OrderRepository $orderRepository, UserRepository $userRepository)
    {
        $this->orderRepository = $orderRepository;
        $this->userRepository = $userRepository;
    }

    /**
     * @param int $userId
     * @return int
     */
    public function execute(int $userId): int
    {
        $user = $this->userRepository->findById($userId);
        if (!($user)) {
            throw new NotFoundHttpException("User not found");
        }

        $order = new Order();
        $order->setUser($user);
        $this->orderRepository->save($order);
        return $order->getId();
    }

}
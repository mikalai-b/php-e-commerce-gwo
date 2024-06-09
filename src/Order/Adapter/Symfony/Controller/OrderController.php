<?php

namespace App\Order\Adapter\Symfony\Controller;


use App\Order\Application\UseCase\CreateOrder;
use App\Order\Application\UseCase\AddOrderItem;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class OrderController extends AbstractController
{
    #[Route('/create_order', name: 'create_order', methods: ['POST'])]
    public function createOrderAction(Request $request, CreateOrder $createOrder): JsonResponse
    {
        $orderId = $createOrder->execute((int) $request->headers->get('userId'));
        return new JsonResponse(["order_id" => $orderId]);
    }

    /**
     * @param int $orderId
     * @param int $productId
     * @param AddOrderItem $addOrderItem
     * @return JsonResponse
     * @throws \Exception
     */
    #[Route('/add_item/{orderId}/{productId}', name: 'add_item', methods: ['POST'])]
    public function addItemAction(int $orderId, int $productId, AddOrderItem $addOrderItem): JsonResponse
    {
        $addOrderItem->execute($orderId, $productId);
        return new JsonResponse();
    }
}
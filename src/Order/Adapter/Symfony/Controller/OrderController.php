<?php

namespace App\Order\Adapter\Symfony\Controller;


use App\Order\Application\UseCase\CreateOrder;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class OrderController extends AbstractController
{
    #[Route('/create_order', name: 'create_order', methods: ['POST'])]
    public function createOrderAction(Request $request, CreateOrder $createOrder): JsonResponse
    {
//        var_dump(getenv('APP_ENV'));
//        die();
        $orderId = $createOrder->execute((int) $request->headers->get('userId'));
        return new JsonResponse(["order_id" => $orderId]);
    }

}
<?php

namespace App\Order\Adapter\Doctrine;

use App\Order\Domain\Model\Order;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class OrderRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Order::class);
    }

    public function findById(int $id): Order
    {
        $order = $this->find($id);
        if(is_null($order)) {
            throw new NotFoundHttpException('Order not found');
        }
        return $order;
    }

    public function save(Order $order): void
    {
        $this->getEntityManager()->persist($order);
        $this->getEntityManager()->flush();
    }
}
<?php

namespace App\Order\Adapter\Doctrine;

use App\Order\Domain\Model\Order;
use App\Order\Domain\Model\OrderItem;
use App\Product\Domain\Model\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class OrderItemRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, OrderItem::class);
    }

    public function findByOrderAndProduct(Order $order, Product $product): ?OrderItem
    {
        return $this->findOneBy(["order" => $order, "product" => $product]);
    }

    /**
     * @throws \Exception
     */
    public function save(OrderItem $orderItem)
    {
        if ($orderItem->getQuantity() <= 0) {
            throw new \Exception( "Quantity couldn't be 0 or less");
        }
        $this->getEntityManager()->persist($orderItem);
        $this->getEntityManager()->flush();
    }

}
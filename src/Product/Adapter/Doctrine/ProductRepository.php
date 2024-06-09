<?php

namespace App\Product\Adapter\Doctrine;

use App\Product\Domain\Model\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }

    public function findById(int $id): Product
    {
        $product = $this->find($id);
        if (is_null($product)) {
            throw new NotFoundHttpException('Product not found');
        }
        return $product;
    }
}
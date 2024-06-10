<?php

namespace App\Promotion\Adapter\Doctrine;

use App\Promotion\Domain\Model\Promotion;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class PromotionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Promotion::class);
    }
    public function findById(int $id): Promotion
    {
        $promotion = $this->find($id);
        if (is_null($promotion)) {
            throw new NotFoundHttpException('Promotion not found');
        }

        return $promotion;
    }
}
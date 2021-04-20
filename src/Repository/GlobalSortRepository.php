<?php

namespace App\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Food|null find($id, $lockMode = null, $lockVersion = null)
 * @method Food|null findOneBy(array $criteria, array $orderBy = null)
 * @method Food[]    findAll()
 * @method Food[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GlobalSortRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Food::class);
    }
}
<?php

namespace App\Repository;

use App\Entity\Evolution;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Evolution>
 *
 * @method Evolution|null find($id, $lockMode = null, $lockVersion = null)
 * @method Evolution|null findOneBy(array $criteria, array $orderBy = null)
 * @method Evolution[]    findAll()
 * @method Evolution[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EvolutionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Evolution::class);
    }
}

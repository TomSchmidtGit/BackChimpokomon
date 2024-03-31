<?php

namespace App\Repository;

use App\Entity\Chimpokomon;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Chimpokomon>
 *
 * @method Chimpokomon|null find($id, $lockMode = null, $lockVersion = null)
 * @method Chimpokomon|null findOneBy(array $criteria, array $orderBy = null)
 * @method Chimpokomon[]    findAll()
 * @method Chimpokomon[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ChimpokomonRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Chimpokomon::class);
    }
}

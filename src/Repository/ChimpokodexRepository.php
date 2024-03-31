<?php

namespace App\Repository;

use App\Entity\Chimpokodex;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Chimpokodex>
 *
 * @method Chimpokodex|null find($id, $lockMode = null, $lockVersion = null)
 * @method Chimpokodex|null findOneBy(array $criteria, array $orderBy = null)
 * @method Chimpokodex[]    findAll()
 * @method Chimpokodex[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ChimpokodexRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Chimpokodex::class);
    }

   /**
    * @return Chimpokodex[] Returns an array of Chimpokodex objects
    */
   public function findAllByStatus($status): array
   {
       return $this->createQueryBuilder('c')
           ->andWhere('c.status = :status')
           ->setParameter('status', $status)
           ->getQuery()
           ->getResult()
       ;
   }

   public function findByStatus($status, $id): array
   {
        return $this->createQueryBuilder('c')
        ->andWhere('c.status = :status', 'c.id = :id')
        ->setParameter('status', $status)
        ->setParameter('id', $id)
        ->getQuery()
        ->getResult();
   }
}

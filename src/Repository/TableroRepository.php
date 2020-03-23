<?php

namespace App\Repository;

use App\Entity\Tablero;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Tablero|null find($id, $lockMode = null, $lockVersion = null)
 * @method Tablero|null findOneBy(array $criteria, array $orderBy = null)
 * @method Tablero[]    findAll()
 * @method Tablero[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TableroRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Tablero::class);
    }

    // /**
    //  * @return Tablero[] Returns an array of Tablero objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('t.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Tablero
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}

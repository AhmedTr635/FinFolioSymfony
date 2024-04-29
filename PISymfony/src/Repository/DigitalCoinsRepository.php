<?php

namespace App\Repository;

use App\Entity\DigitalCoins;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<DigitalCoins>
 *
 * @method DigitalCoins|null find($id, $lockMode = null, $lockVersion = null)
 * @method DigitalCoins|null findOneBy(array $criteria, array $orderBy = null)
 * @method DigitalCoins[]    findAll()
 * @method DigitalCoins[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DigitalCoinsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DigitalCoins::class);
    }

//    /**
//     * @return DigitalCoins[] Returns an array of DigitalCoins objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('d')
//            ->andWhere('d.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('d.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?DigitalCoins
//    {
//        return $this->createQueryBuilder('d')
//            ->andWhere('d.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}

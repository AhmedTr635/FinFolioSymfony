<?php

namespace App\Repository;

use App\Entity\ActifCourant;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ActifCourant>
 *
 * @method ActifCourant|null find($id, $lockMode = null, $lockVersion = null)
 * @method ActifCourant|null findOneBy(array $criteria, array $orderBy = null)
 * @method ActifCourant[]    findAll()
 * @method ActifCourant[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ActifCourantRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ActifCourant::class);
    }

//    /**
//     * @return ActifCourant[] Returns an array of ActifCourant objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('a.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?ActifCourant
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}

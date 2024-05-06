<?php

namespace App\Repository;

use App\Entity\ActifNonCourant;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ActifNonCourant>
 *
 * @method ActifNonCourant|null find($id, $lockMode = null, $lockVersion = null)
 * @method ActifNonCourant|null findOneBy(array $criteria, array $orderBy = null)
 * @method ActifNonCourant[]    findAll()
 * @method ActifNonCourant[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ActifNonCourantRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ActifNonCourant::class);
    }

//    /**
//     * @return ActifNonCourant[] Returns an array of ActifNonCourant objects
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

//    public function findOneBySomeField($value): ?ActifNonCourant
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}

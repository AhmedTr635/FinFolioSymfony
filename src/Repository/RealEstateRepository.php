<?php

namespace App\Repository;

use App\Entity\RealEstate;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<RealEstate>
 *
 * @method RealEstate|null find($id, $lockMode = null, $lockVersion = null)
 * @method RealEstate|null findOneBy(array $criteria, array $orderBy = null)
 * @method RealEstate[]    findAll()
 * @method RealEstate[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RealEstateRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RealEstate::class);
    }

//    /**
//     * @return RealEstate[] Returns an array of RealEstate objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('r')
//            ->andWhere('r.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('r.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?RealEstate
//    {
//        return $this->createQueryBuilder('r')
//            ->andWhere('r.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
    public function findRealEstateById($id): ?RealEstate
    {
        return $this->find($id);
    }
}
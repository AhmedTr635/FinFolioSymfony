<?php

namespace App\Repository;

use App\Entity\Investissement;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Investissement>
 *
 * @method Investissement|null find($id, $lockMode = null, $lockVersion = null)
 * @method Investissement|null findOneBy(array $criteria, array $orderBy = null)
 * @method Investissement[]    findAll()
 * @method Investissement[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class InvestissementRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Investissement::class);
    }

//    /**
//     * @return Investissement[] Returns an array of Investissement objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('i')
//            ->andWhere('i.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('i.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Investissement
//    {
//        return $this->createQueryBuilder('i')
//            ->andWhere('i.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
    public function getSumByRealEstateId(?int $realEstateId)
    {
        $qb = $this->createQueryBuilder('i')
            ->select('SUM(i.montant) as total')
            ->andWhere('i.reId = :realEstateId')
            ->setParameter('realEstateId', $realEstateId);

        $result = $qb->getQuery()->getSingleScalarResult();

        return $result;
    }
    public function getTotalInvestmentByRealEstateId(int $realEstateId): float
    {
        return $this->createQueryBuilder('i')
            ->select('SUM(i.montant) as totalInvestment')
            ->andWhere('i.reId = :realEstateId')
            ->setParameter('realEstateId', $realEstateId)
            ->getQuery()
            ->getSingleScalarResult();
    }
    public function getRealEstateValueById(int $realEstateId): float
    {
        return $this->getEntityManager()->createQueryBuilder()
            ->select('r.valeur')
            ->from('App\Entity\RealEstate', 'r')
            ->andWhere('r.id = :realEstateId')
            ->setParameter('realEstateId', $realEstateId)
            ->getQuery()
            ->getSingleScalarResult();
    }
    public function getTotalInvestmentByUserId(int $userId): ?float
    {
        $qb = $this->createQueryBuilder('i')
            ->select('SUM(i.montant) as totalInvestment')
            ->andWhere('i.userId = :userId')
            ->setParameter('userId', $userId);

        return $qb->getQuery()->getSingleScalarResult();
    }
    public function getTotalInvestmentByMonth(\DateTimeInterface $month): ?float
    {
        $monthStart = $month->format('Y-m-01');
        $monthEnd = $month->format('Y-m-t');

        $qb = $this->createQueryBuilder('i')
            ->select('SUM(i.montant) as totalInvestment')
            ->andWhere('i.date BETWEEN :monthStart AND :monthEnd')
            ->setParameter('monthStart', $monthStart)
            ->setParameter('monthEnd', $monthEnd);

        return $qb->getQuery()->getSingleScalarResult();
    }
    public function getTotalRoiInvestmentsByUserId(int $userId): ?float
    {
        $qb = $this->createQueryBuilder('i')
            ->select('SUM((i.montant * i.ROI) / 100) as totalRoiInvestments')
            ->andWhere('i.userId = :userId')
            ->setParameter('userId', $userId);

        return $qb->getQuery()->getSingleScalarResult();
    }

    public function findByUserId(int $userId): array
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.userId = :userId')
            ->setParameter('userId', $userId)
            ->getQuery()
            ->getResult();
    }
    public function totalTaxByUserId(int $userId): ?float
    {
        $qb = $this->createQueryBuilder('i')
            ->select('SUM(i.tax) as totalTax')
            ->andWhere('i.userId = :userId')
            ->setParameter('userId', $userId);

        return $qb->getQuery()->getSingleScalarResult();
    }



}

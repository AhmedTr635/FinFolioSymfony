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
    public function findByUserId(int $userId): array
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.userId = :userId')
            ->setParameter('userId', $userId)
            ->getQuery()
            ->getResult();
    }
    // src/Repository/DigitalCoinsRepository.php

// ...


    // ...

    public function findByUserIdAndDateVenteIsNull(int $userId): array
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.userId = :userId')
            ->andWhere('i.dateVente IS NULL')
            ->setParameter('userId', $userId)
            ->getQuery()
            ->getResult();
    }

    public function findByUserIdAndDateVenteIsNotNull(int $userId): array
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.userId = :userId')
            ->andWhere('i.dateVente IS NOT NULL')
            ->setParameter('userId', $userId)
            ->getQuery()
            ->getResult();
    }
    public function findTotalMontantWhereDateVenteIsNull(): float
    {
        return $this->createQueryBuilder('d')
            ->select('SUM(d.montant)')
            ->where('d.dateVente IS NULL')
            ->getQuery()
            ->getSingleScalarResult();
    }
    public function findTotalRoiWhereDateVenteIsNotNull(): float
    {
        return $this->createQueryBuilder('d')
            ->select('SUM(d.ROI)')
            ->where('d.dateVente IS NOT NULL')
            ->getQuery()
            ->getSingleScalarResult();
    }
    public function findMontantByUserIdGroupByCode(int $userId): array
    {
        return $this->createQueryBuilder('d')
            ->select('d.code, SUM(d.montant) as totalMontant')
            ->andWhere('d.userId = :userId')
            ->groupBy('d.code')
            ->setParameter('userId', $userId)
            ->getQuery()
            ->getResult();
    }
    public function findTotalMontantByUserIdAndCode(int $userId, string $code): float
    {
        return $this->createQueryBuilder('d')
            ->select('SUM(d.montant)')
            ->andWhere('d.userId = :userId')
            ->andWhere('d.code = :code')
            ->setParameter('userId', $userId)
            ->setParameter('code', $code)
            ->getQuery()
            ->getSingleScalarResult();
    }
    public function findTotalMontantWhereDateVenteIsNullByUserId(int $userId): float
    {
        return $this->createQueryBuilder('d')
            ->select('SUM(d.montant)')
            ->andWhere('d.userId = :userId')
            ->andWhere('d.dateVente IS NULL')
            ->setParameter('userId', $userId)
            ->getQuery()
            ->getSingleScalarResult();
    }
    public function findTotalRoiWhereDateVenteIsNotNullByUserId(int $userId): float
    {
        return $this->createQueryBuilder('d')
            ->select('SUM(d.ROI)')
            ->andWhere('d.userId = :userId')
            ->andWhere('d.dateVente IS NOT NULL')
            ->setParameter('userId', $userId)
            ->getQuery()
            ->getSingleScalarResult();
    }
    public function findTotalMontantByUserId(int $userId): float
    {
        return $this->createQueryBuilder('d')
            ->select('SUM(d.montant)')
            ->andWhere('d.userId = :userId')
            ->setParameter('userId', $userId)
            ->getQuery()
            ->getSingleScalarResult();
    }
    public function findTotalRoiByUserId(int $userId): float
    {
        return $this->createQueryBuilder('d')
            ->select('SUM(d.ROI)')
            ->andWhere('d.userId = :userId')
            ->getQuery()
            ->getSingleScalarResult();
    }
    public function findTotalMontantByUserIdAndDateVenteIsNotNull(int $userId): float
    {
        return $this->createQueryBuilder('d')
            ->select('SUM(d.montant)')
            ->andWhere('d.userId = :userId')
            ->andWhere('d.dateVente IS NOT NULL')
            ->setParameter('userId', $userId)
            ->getQuery()
            ->getSingleScalarResult();
    }
    public function findTotalRoiByUserIdAndDateVenteIsNotNull(int $userId): float
    {
        return $this->createQueryBuilder('d')
            ->select('SUM(d.ROI)')
            ->andWhere('d.userId = :userId')
            ->andWhere('d.dateVente IS NOT NULL')
            ->setParameter('userId', $userId)
            ->getQuery()
            ->getSingleScalarResult();
    }
    public function findTotalMontantByUserIdAndDateVenteIsNull(int $userId): float
    {
        return $this->createQueryBuilder('d')
            ->select('SUM(d.montant)')
            ->andWhere('d.userId = :userId')
            ->andWhere('d.dateVente IS NULL')
            ->setParameter('userId', $userId)
            ->getQuery()
            ->getSingleScalarResult();
    }



}


<?php

namespace App\Repository;

use App\Entity\TotalTax;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<TotalTax>
 *
 * @method TotalTax|null find($id, $lockMode = null, $lockVersion = null)
 * @method TotalTax|null findOneBy(array $criteria, array $orderBy = null)
 * @method TotalTax[]    findAll()
 * @method TotalTax[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TotalTaxRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TotalTax::class);
    }


//    /**
//     * @return TotalTax[] Returns an array of TotalTax objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('t.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?TotalTax
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }

    /**
     * @throws NonUniqueResultException
     */
    public function getTotalTaxValue(): ?float
    {
        $queryBuilder = $this->createQueryBuilder('t')
            ->select('t.total');

        $result = $queryBuilder->getQuery()->getOneOrNullResult();

        return $result ? $result['total'] : null;
    }
}

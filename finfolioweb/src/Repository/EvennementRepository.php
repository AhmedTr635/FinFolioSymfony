<?php

namespace App\Repository;

use App\Entity\Evennement;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Evennement>
 *
 * @method Evennement|null find($id, $lockMode = null, $lockVersion = null)
 * @method Evennement|null findOneBy(array $criteria, array $orderBy = null)
 * @method Evennement[]    findAll()
 * @method Evennement[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EvennementRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Evennement::class);
    }


    /**
     * @throws NonUniqueResultException
     */
    public function findUpcomingEvent(): ?Evennement
    {
        return $this->createQueryBuilder('e')
            ->where('e.date >= :currentDate')
            ->orderBy('e.date', 'ASC')
            ->setParameter('currentDate', new \DateTime())
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }


    public function getTotalDonationsByEventId(): array
    {
        $result = $this->createQueryBuilder('e')
            ->select('e.id, SUM(d.montant_user) AS totalDonations')
            ->leftJoin('e.dons', 'd')
            ->groupBy('e.id')
            ->getQuery()
            ->getResult();

        // Convert totalDonations to float values
        foreach ($result as &$row) {
            $row['totalDonations'] = (float)$row['totalDonations'];
        }

        return $result;
    }





//    /**
//     * @return Evennement[] Returns an array of Evennement objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('e')
//            ->andWhere('e.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('e.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Evennement
//    {
//        return $this->createQueryBuilder('e')
//            ->andWhere('e.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}

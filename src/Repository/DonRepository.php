<?php

namespace App\Repository;

use App\Entity\Don;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Don>
 *
 * @method Don|null find($id, $lockMode = null, $lockVersion = null)
 * @method Don|null findOneBy(array $criteria, array $orderBy = null)
 * @method Don[]    findAll()
 * @method Don[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DonRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Don::class);
    }


    /**
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    public function getTotalDonationForEvent($eventId): float
    {
        return $this->createQueryBuilder('d')
            ->select('SUM(d.montant_user)') // Assuming 'montantUser' is the property representing the donation amount
            ->andWhere('d.evenement_id = :eventId')
            ->setParameter('eventId', $eventId)
            ->getQuery()
            ->getSingleScalarResult() ?? 0; // Return 0 if no donations found
    }


    /**
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    public function calculateTotalDonations(): ?float
    {
        $totalDonations = $this->createQueryBuilder('d')
            ->select('SUM(d.montant_user) as total')
            ->getQuery()
            ->getSingleScalarResult();

        return $totalDonations;
    }





//    /**
//     * @return Don[] Returns an array of Don objects
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

//    public function findOneBySomeField($value): ?Don
//    {
//        return $this->createQueryBuilder('d')
//            ->andWhere('d.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
<?php

namespace App\Repository;

use App\Entity\Tax;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Tax>
 *
 * @method Tax|null find($id, $lockMode = null, $lockVersion = null)
 * @method Tax|null findOneBy(array $criteria, array $orderBy = null)
 * @method Tax[]    findAll()
 * @method Tax[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TaxRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Tax::class);
    }

//    /**
//     * @return Tax[] Returns an array of Tax objects
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

//    public function findOneBySomeField($value): ?Tax
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }

    public function getExpensesByTaxType()
    {
        // Fetch all expenses
        $taxes = $this->createQueryBuilder('d')
            ->getQuery()
            ->getResult();

        // Group expenses by tax type and calculate total expenses for each tax type
        $expensesByTaxType = [];
        foreach ($taxes as $tax) {
            $taxType = $tax->getType();
            if (!isset($expensesByTaxType[$taxType])) {
                $expensesByTaxType[$taxType] = 0;
            }
            $expensesByTaxType[$taxType] += $tax->getMontant();
        }

        // Prepare data for the chart
        $labels = array_keys($expensesByTaxType);
        $data = array_values($expensesByTaxType);

        // Return the data with 'tax_type' key included
        return [
            'tax_type' => $labels,
            'total' => $data
        ];
    }

}

<?php

namespace App\Repository;

use App\Entity\Depense;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Depense>
 *
 * @method Depense|null find($id, $lockMode = null, $lockVersion = null)
 * @method Depense|null findOneBy(array $criteria, array $orderBy = null)
 * @method Depense[]    findAll()
 * @method Depense[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DepenseRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Depense::class);
    }

//    /**
//     * @return Depense[] Returns an array of Depense objects
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

//    public function findOneBySomeField($value): ?Depense
//    {
//        return $this->createQueryBuilder('d')
//            ->andWhere('d.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
    public function getExpensesByDepenseType()
    {
        // Fetch all expenses
        $depenses = $this->createQueryBuilder('d')
            ->getQuery()
            ->getResult();

        // Group expenses by tax type and calculate total expenses for each tax type
        $expensesByTaxType = [];
        foreach ($depenses as $depense) {
            $depenseType = $depense->getType();
            if (!isset($expensesByTaxType[$depenseType])) {
                $expensesByTaxType[$depenseType] = 0;
            }
            $expensesByTaxType[$depenseType] += $depense->getMontant();
        }

        // Prepare data for the chart
        $labels = array_keys($expensesByTaxType);
        $data = array_values($expensesByTaxType);

        // Return the data with 'tax_type' key included
        return [
            'depense' => $labels,
            'total' => $data
        ];
    }
    public function findByTerm($term)
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.type LIKE :term')
            ->setParameter('term', '%'.$term.'%')
            ->getQuery()
            ->getResult();
    }

    public function getTotalItemCount()
    {
        // Here, you need to define how to get the total count of items in your database.
        // It could be using Doctrine QueryBuilder or any other method based on your data model.

        // For example, if you have an entity named YourEntity:
        return $this->createQueryBuilder('e')
            ->select('COUNT(e.id)')
            ->getQuery()
            ->getSingleScalarResult();
    }
    public function getExpensesByMonth()
    {
        // Fetch all expenses
        $expenses = $this->createQueryBuilder('d')
            ->getQuery()
            ->getResult();

        // Group expenses by month and calculate total expenses for each month
        $expensesByMonth = [];
        foreach ($expenses as $expense) {
            $monthYear = $expense->getDate()->format('Y-m');
            if (!isset($expensesByMonth[$monthYear])) {
                $expensesByMonth[$monthYear] = 0;
            }
            $expensesByMonth[$monthYear] += $expense->getMontant();
        }

        // Prepare data for the chart
        $labels = array_keys($expensesByMonth);
        $data = array_values($expensesByMonth);

        // Return the data with 'month' key included
        return [
            'month' => $labels,
            'total' => $data
        ];
    }
}
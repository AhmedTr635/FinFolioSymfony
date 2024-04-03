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
    public function searchByType($query)
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.type LIKE :query')
            ->setParameter('query', '%' . $query . '%')
            ->getQuery()
            ->getResult();
    }

    public function findPaginatedData($page = 1, $limit = 10)
    {
        // Calculer l'offset en fonction de la page et de la limite
        $offset = ($page - 1) * $limit;

        // Créer une requête pour récupérer les données paginées
        $query = $this->createQueryBuilder('e')
            // Vous pouvez ajouter d'autres conditions de requête ici si nécessaire
            ->orderBy('e.id', 'ASC')
            ->setFirstResult($offset)
            ->setMaxResults($limit)
            ->getQuery();

        // Utiliser l'objet Paginator pour paginer les résultats de la requête
        $paginator = new Paginator($query, $fetchJoinCollection = true);

        // Retourner l'objet Paginator, contenant les données paginées
        return $paginator;
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
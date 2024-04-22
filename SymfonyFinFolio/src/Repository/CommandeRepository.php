<?php

namespace App\Repository;

use App\Entity\Commande;
use App\Entity\User;
use App\Services\StripeService;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Commande>
 *
 * @method Commande|null find($id, $lockMode = null, $lockVersion = null)
 * @method Commande|null findOneBy(array $criteria, array $orderBy = null)
 * @method Commande[]    findAll()
 * @method Commande[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CommandeRepository extends ServiceEntityRepository
{
    protected $stripeService;

    public function __construct(ManagerRegistry $registry,        StripeService $stripeService)
    {
        parent::__construct($registry, Commande::class);
        $this->stripeService = $stripeService;

    }

//    /**
//     * @return Commande[] Returns an array of Commande objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('c.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Commande
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
    public function intentSecret(string $prix)
    {
        $intent = $this->stripeService->paymentIntent($prix);

        return $intent['client_secret'] ?? null;
    }

    public function stripe(array $stripeParameter, string $prix,string $utilite)
    {
        $resource = null;
        $data = $this->stripeService->stripe($stripeParameter, $prix,$utilite);
//    dd($data);
        if($data) {
            $resource = [
//                'stripeBrand' => $data['payment_method_details']['card']['brand'],
//                'stripeLast4' => $data['payment_method_details']['card']['last4'],
                'stripeId' => $data['id'],
                'stripeStatus' => $data['status'],
                'stripeToken' => $data['client_secret'],
            ];
        }

        return $resource;
    }
    public function create_subscription(array $resource, User $user,string $prix,EntityManagerInterface $em)
    {
        $commande = new Commande();
        $commande->setUser($user);
        $user->setTypeCompte("premium");
        $commande->setPrice($prix);
        $commande->setReference(uniqid('', false));
        $commande->setBrandStripe("visa");
        $commande->setLast4Stripe("4242");
        $commande->setIdChargeStripe($resource['stripeId']);
        $commande->setStripeToken($resource['stripeToken']);
        $commande->setStatusStripe($resource['stripeStatus']);
        $em->persist($commande);
        $em->flush();
    }
}

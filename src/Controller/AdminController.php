<?php

namespace App\Controller;

use App\Form\UserModifType;
use App\Form\UserProfile;
use App\Form\UserProfileType;
use App\Repository\CommandeRepository;
use App\Services\QrCodeService;
use DateTime;
use Doctrine\ORM\EntityManager;
use http\Exception\UnexpectedValueException;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\Form\FormError;
use App\Entity\User;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

use App\Form\UserLogin;
use App\Form\UserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Twilio\Rest\Client;

#[Route('/admin')]

class AdminController extends AbstractController
{

    #[Route('/', name: 'app_user_index', methods: ['GET', 'POST'])]
    public function index(PaginatorInterface $paginator,UserRepository $userRepository, Request $request, EntityManagerInterface $entityManager, ManagerRegistry $managerRegistry,SluggerInterface $slugger): Response
    {
        $user = new User();
        $formModifier=$this->createForm(UserType::class);
        $form = $this->createForm(UserType::class, $user, [
            'required' => false, // Désactive la validation automatique des champs vides
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $existingUser = $managerRegistry->getRepository(User::class)->findOneBy(['email' => $user->getEmail()]);

            if ($existingUser) {
                $form->get('email')->addError(new FormError('Cette adresse e-mail est déjà utilisée. Veuillez en choisir une autre.'));
                return $this->render('user/index.html.twig', [
                    'form' => $form->createView(),
                ]);
            }
            else{
                $image = $form->get('image')->getData();

                if ($image) {
                    $originalFilename = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);
                    // this is needed to safely include the file name as part of the URL
                    $safeFilename = $slugger->slug($originalFilename);
                    $newFilename = $safeFilename.'-'.uniqid().'.'.$image->guessExtension();

                    // Move the file to the directory where brochures are stored
                    try {
                        $image->move(
                            $this->getParameter('brochures_directory'),
                            $newFilename
                        );
                    } catch (FileException $e) {
                        // ... handle exception if something happens during file upload
                    }

                    $user->setImage($newFilename);}

                $user->setRate(2);
                $user->setNbcredit(0);
                $user->setRole("admin");
                $user->setSolde(2000);
                $user->setRate(2);
                $user->setStatut("active");
                $user->setAdresse("Tunisie");

                $user->setPassword(sha1($user->getPassword()));
                $user->setDatepunition(new \DateTime('0000-00-00'));
                $user->setTotalTax(0);
                $entityManager->persist($user);
                $entityManager->flush();

                return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
            }}

        $repository = $managerRegistry->getRepository(User::class);

        $adminsCount = $repository->count(['role' => 'admin']);
        $usersCount = $repository->count(['role' => 'user']);
        $activeCount = $repository->count(['statut' => 'active']);
        $desactiveCount = $repository->count(['statut' => 'desactive']);
        $users=$userRepository->findAll();
        $users= $paginator->paginate(
            $users,
            $request->query->getInt('page',1)  ,
            5
        );
        $paginationTemplate = '@KnpPaginator/Pagination/twitter_bootstrap_v4_pagination.html.twig';


        return $this->render('user/index.html.twig', [
            'users' => $users,
            'paginationTemplate' => $paginationTemplate,
            'form' => $form->createView() ,
            //'formModifier' =>$formModifier->createView() ,
            'adminsCount' => $adminsCount ,
            'usersCount' => $usersCount ,
            'activeCount' => $activeCount ,
            'desactiveCount' => $desactiveCount
        ]);
    }

    #[Route('/{id}', name: 'app_user_delete', methods: ['POST','GET'])]
    public function delete(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $entityManager->remove($user);
            // dd("dali");
            $entityManager->flush();

        }

        return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
    }
    #[Route('/{id}/edit', name: 'app_user_edit', methods: ['GET', 'POST','PUT'])]
    public function edit(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(UserModifType::class, $user, array(
            'method'=>'put'
        ));        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {
            dd('cbn');
            $statut = $user->getStatut();
            $datepunition = $user->getDatePunition();
            if ($statut === 'desactive' && $datepunition <= new \DateTime()) {
                // Si la validation échoue, ajoutez un message d'erreur
                $this->addFlash('error', 'La date de punition doit être postérieure à la date actuelle pour desactiver le statut');
                // Rediriger vers la page du formulaire avec le message d'erreur
            }else{
                if($user->getStatut()==='active'||$user->getStatut()==='ban' )
                    $user->setDatepunition(new \DateTime('0000-00-00'));
//          if($user->getStatut()!='active'&& $user->getStatut()!='ban'&& $user->getStatut()!='desactive' )
//          { $user->setStatut("active");
//                $user->setDatepunition(new \DateTime('0000-00-00'));}

                $entityManager->flush();

                return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);}
        }

        return $this->renderForm('user/edit.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }
    #[Route("/editUserJson/{id}", methods: ["POST"])]
    public function editUser(Request $request, $id, UserRepository $userRepository, EntityManagerInterface $entityManager): JsonResponse
    {
        $user = $userRepository->find($id);
        if (!$user) {
            return new JsonResponse(['error' => 'User not found'], JsonResponse::HTTP_NOT_FOUND);
        }

        $statut = $request->request->get("statut");
        $datepunition = $request->request->get("datepunition");

        // Mettez à jour les propriétés de l'utilisateur
        $user->setStatut($statut);
        $user->setDatePunition(new \DateTime($datepunition));

        // Validez les données, en fonction de vos besoins
        // Exemple : vous pouvez ajouter des contraintes de validation pour le statut et la date de punition

        // Enregistrez les modifications dans la base de données
        $entityManager->flush();

        // Renvoyez une réponse JSON avec les données mises à jour de l'utilisateur
        return new JsonResponse($user);
    }



}
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


#[Route('/user')]
class UserController extends AbstractController
{
    private $accountSid;
    private $authToken;
    private $twilioPhoneNumber;
    private $twilioClient;
    private TokenStorageInterface $tokenStorage;
    public function __construct(TokenStorageInterface $tokenStorage, string $accountSid, string $authToken, string $twilioPhoneNumber)
    {
        $this->tokenStorage = $tokenStorage;
        $this->accountSid = $accountSid;
        $this->authToken = $authToken;
        $this->twilioPhoneNumber = $twilioPhoneNumber;
        $this->twilioClient = new Client($accountSid, $authToken);
    }
//    #[Route('/', name: 'app_user_index', methods: ['GET', 'POST'])]
//    public function index(PaginatorInterface $paginator,UserRepository $userRepository, Request $request, EntityManagerInterface $entityManager, ManagerRegistry $managerRegistry,SluggerInterface $slugger): Response
//    {
//        $user = new User();
//        $formModifier=$this->createForm(UserType::class);
//        $form = $this->createForm(UserType::class, $user, [
//            'required' => false, // Désactive la validation automatique des champs vides
//        ]);
//        $form->handleRequest($request);
//
//        if ($form->isSubmitted() && $form->isValid()) {
//            $existingUser = $managerRegistry->getRepository(User::class)->findOneBy(['email' => $user->getEmail()]);
//
//            if ($existingUser) {
//                $form->get('email')->addError(new FormError('Cette adresse e-mail est déjà utilisée. Veuillez en choisir une autre.'));
//                return $this->render('user/index.html.twig', [
//                    'form' => $form->createView(),
//                ]);
//            }
//            else{
//                $image = $form->get('image')->getData();
//
//                if ($image) {
//                $originalFilename = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);
//                // this is needed to safely include the file name as part of the URL
//                $safeFilename = $slugger->slug($originalFilename);
//                $newFilename = $safeFilename.'-'.uniqid().'.'.$image->guessExtension();
//
//                // Move the file to the directory where brochures are stored
//                try {
//                    $image->move(
//                        $this->getParameter('brochures_directory'),
//                        $newFilename
//                    );
//                } catch (FileException $e) {
//                    // ... handle exception if something happens during file upload
//                }
//
//                $user->setImage($newFilename);}
//
//            $user->setRate(2);
//            $user->setNbcredit(0);
//            $user->setRole("admin");
//            $user->setSolde(2000);
//            $user->setRate(2);
//            $user->setStatut("active");
//            $user->setAdresse("Tunisie");
//
//            $user->setPassword(sha1($user->getPassword()));
//            $user->setDatepunition(new \DateTime('0000-00-00'));
//            $user->setTotalTax(0);
//            $entityManager->persist($user);
//            $entityManager->flush();
//
//            return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
//        }}
//
//        $repository = $managerRegistry->getRepository(User::class);
//
//        $adminsCount = $repository->count(['role' => 'admin']);
//        $usersCount = $repository->count(['role' => 'user']);
//        $activeCount = $repository->count(['statut' => 'active']);
//        $desactiveCount = $repository->count(['statut' => 'desactive']);
//        $users=$userRepository->findAll();
//        $users= $paginator->paginate(
//            $users,
//            $request->query->getInt('page',1)  ,
//            5
//        );
//        $paginationTemplate = '@KnpPaginator/Pagination/twitter_bootstrap_v4_pagination.html.twig';
//
//
//        return $this->render('user/index.html.twig', [
//            'users' => $users,
//            'paginationTemplate' => $paginationTemplate,
//            'form' => $form->createView() ,
//            //'formModifier' =>$formModifier->createView() ,
//            'adminsCount' => $adminsCount ,
//            'usersCount' => $usersCount ,
//            'activeCount' => $activeCount ,
//            'desactiveCount' => $desactiveCount
//        ]);
//    }
//    #[Route('/{id}/edit', name: 'app_user_edit', methods: ['POST','GET'])]
//    public function edit(Request $request, User $user,EntityManagerInterface $entityManager): Response
//    {
//        // Créez une instance de votre formulaire UserModifType
//        $form = $this->createForm(UserModifType::class, $user);
//
//        // Traitez la soumission du formulaire
//        $form->handleRequest($request);
//       // dd($user);
//        // Vérifiez si le formulaire est soumis et valide
//        if ($form->isSubmitted() && $form->isValid()) {
//            dd($user);
//
//            $entityManager->flush();
//
//            // Redirigez l'utilisateur vers une autre page ou affichez un message de succès
//            return $this->redirectToRoute('app_user_index'); // Remplacez 'user_list' par le nom de la route vers la liste des utilisateurs
//        }
//
//        // Si le formulaire n'est pas soumis ou n'est pas valide, affichez à nouveau le formulaire avec les erreurs éventuelles
//        return $this->render('user/edit.html.twig', [
//            'formM' => $form->createView(),
//            'user' => $user, // Vous pouvez transmettre l'entité utilisateur au template si nécessaire
//        ]);
//    }
    #[Route('/get_user_info/{id}', name: 'get_user_info', methods: ['GET'])]
    public function getUserInfo($id, UserRepository $userRepository): JsonResponse
    {
        $user = $userRepository->find($id);

        if (!$user) {
            return new JsonResponse(['error' => 'User not found'], 404);
        }

        return new JsonResponse([
            'nom' => $user->getNom(),
            'prenom' => $user->getPrenom(),
            'email' => $user->getEmail(),
            'numtel' => $user->getNumtel(),
            'statut' => $user->getStatut(),
            'datepunition' => $user->getDatepunition(),

            // Ajoutez d'autres propriétés de l'utilisateur selon vos besoins
        ]);
    }

    //------------------------SignUp------------------------------------
    //------------------------SignUp------------------------------------
    //------------------------SignUp------------------------------------
    //------------------------SignUp------------------------------------

    #[Route('/register', name: 'signup')]
    public function register(Request $request ,ManagerRegistry $managerRegistry, SessionInterface $session,QrCodeService $qrCode,SluggerInterface $slugger): Response
    {

        $user = new User();
        $form = $this->createForm(UserType::class, $user, [
            'required' => false, // Désactive la validation automatique des champs vides
        ]);
        $form->handleRequest($request);

        //dd($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $existingUser = $managerRegistry->getRepository(User::class)->findOneBy(['email' => $user->getEmail()]);

            if ($existingUser) {

                $form->get('email')->addError(new FormError('Cette adresse e-mail est déjà utilisée. Veuillez en choisir une autre.'));
                return $this->render('user/register.html.twig', [
                    'form' => $form->createView(),
                ]);
            }
            else {

                $image = $form->get('image')->getData();

                // this condition is needed because the 'brochure' field is not required
                // so the PDF file must be processed only when a file is uploaded
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



                $code = $this->generateRecoveryCode(5);
                $qrCode->qrcode($code);
                $this->envoyerMail($user);
                $user->setCode($code);
                $verificationData = [

                    'user' => $user,
                ];
                $session->set('verification_data', $verificationData);
                return $this->redirectToRoute("verification_page");
            }


        }

        return $this->renderForm('user/register.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }
    #[Route('/verification', name: 'verification_page')]
    public function verificationPage(Request $request, SessionInterface $session, EntityManagerInterface $entityManager)
    {

        $verificationData = $session->get('verification_data');
        $user = $verificationData['user'];
        $verificationCode = $user->getCode();
        $errorMessage = null;
        // Comparer les codes de vérification
        if ($request->isMethod('POST')) {
            // Retrieve submitted verification code from the request
            $submittedVerificationCode = $request->request->get('verificationCode');

            // Retrieve verification code from session

            // Compare submitted verification code with stored verification code
            if ($submittedVerificationCode == $verificationCode) {
                // Verification successful, update user data and redirect to login page
                $user->setRate(2);
                $user->setNbcredit(0);
                $user->setRole("user");
                $user->setSolde(2000);
                $user->setRate(2);
                $user->setStatut("active");


                $user->setAdresse("Tunisie");
                $user->setPassword(sha1($user->getPassword())); // Note: Consider using modern encryption methods
                $user->setDatepunition(new \DateTime('0000-00-00'));
                $user->setTotalTax(0);
                $user->setTypeCompte('simple');
                $entityManager->persist($user);

                $entityManager->flush();

                // Redirect user to login page after successful verification
                return $this->redirectToRoute('app_login');
            }else {
            $errorMessage="Le code de vérification est incorrect";
        }}

        return $this->renderForm('user/verification.html.twig',[
            'user'=>$user,
            'errorMessage'=>$errorMessage
        ]);


    }

    /****************************************************
     Mot de passe Oublié
    ****************************************************
     */
    #[Route('/passwordOublie', name: 'mdp_page')]
    public function passwordOublie(Request $request,ManagerRegistry $managerRegistry,  SessionInterface $session,QrCodeService $qrCode)
    {
        $errorMessage=null;

        // Comparer les codes de vérification
        if ($request->isMethod('POST')) {
            // Retrieve submitted verification code from the request
            $submittedMail = $request->request->get('mail');
            $existingUser = $managerRegistry->getRepository(User::class)->findOneBy(['email' => $submittedMail]);

            // Retrieve verification code from session

            // Compare submitted verification code with stored verification code
            if (!$existingUser) {

                $errorMessage="Votre mail n'existe pas ";

            }
            else {
                // Redirect user to login page after successful verification
                $code=$this->generateRecoveryCode(5);
                $existingUser->setCode($code);
                $qrCode->qrcode($code);
                $this->envoyerMail($existingUser);
                $user_modif = [

                    'user' => $existingUser,
                ];
                $session->set('user_modif', $user_modif);
                return $this->redirectToRoute('verification_code_page');
            }}

        return $this->renderForm('user/passwordOublie.html.twig',[
            'errorMessage'=>$errorMessage
        ]);


    }
    #[Route('/changerPassword', name: 'mdpCh_page')]
    public function passwordOublieChanger(Request $request,ManagerRegistry $managerRegistry,  EntityManagerInterface $entityManager ,SessionInterface $session)
    {

        $user_modif = $session->get('user_modif');
        $user = $user_modif['user'];

        // Comparer les codes de vérification
        if ($request->isMethod('POST')) {
            // Retrieve submitted verification code from the request
            $submittedPassword = $request->request->get('password');
            $existingUser = $managerRegistry->getRepository(User::class)->findOneBy(['email' => $user->getEmail()]);
            $existingUser->setPassword(sha1($submittedPassword));
            $entityManager->flush();
            $this->twilioClient->messages->create(
                "+21653604045",
                [
                    'from' => $this->twilioPhoneNumber,
                    'body' => "Ton mot de passe  a été changé avec succes."
                ]
            );
            $this->addFlash('success', 'Mot de passe changé avec succes.');


            return $this->redirectToRoute('app_login');
            }

        return $this->renderForm('user/confirmerPassword.html.twig',[
        ]);


    }


    #[Route('/verificationPassword', name: 'verification_code_page')]
    public function verificationPagePassword(Request $request, SessionInterface $session, EntityManagerInterface $entityManager)
    {

        $user_modif = $session->get('user_modif');
        $user = $user_modif['user'];
        $verificationCode = $user->getCode();
        $errorMessage = null;
        // Comparer les codes de vérification
        if ($request->isMethod('POST')) {
            // Retrieve submitted verification code from the request
            $submittedVerificationCode = $request->request->get('verificationCode');

            // Retrieve verification code from session

            // Compare submitted verification code with stored verification code
            if ($submittedVerificationCode == $verificationCode) {
                // Verification successful, update user data and redirect to login page

                // Redirect user to login page after successful verification
                return $this->redirectToRoute('mdpCh_page');
            }else {
                $errorMessage="Le code de vérification est incorrect";
            }}

        return $this->renderForm('user/verificationcodePassword.html.twig',[
            'user'=>$user,
            'errorMessage'=>$errorMessage
        ]);


    }




    /****************************************************
    Fin  Mot de passe Oublié
     ****************************************************
     */



    //------------------------Login------------------------------------
    //------------------------Login------------------------------------
    //------------------------Login------------------------------------
    //------------------------Login------------------------------------



    #[Route('/frontUser', name:'frontUser')]
    public function template (Request $request,ManagerRegistry $managerRegistry,EntityManagerInterface $entityManager){
//        $this->twilioClient->messages->create(
//                "+21653604045",
//                [
//                    'from' => $this->twilioPhoneNumber,
//                    'body' => "Ton mot de passe  a été changé avec succes."
//                ]
//            );
        if ($request->isMethod('POST')) {
            // Retrieve submitted verification code from the request
            $submittedVerificationCode = $request->request->get('verif');
dd($submittedVerificationCode);


        }

            return $this->renderForm('user/SMS.html.twig'

        );

    }
    #[Route('/abb', name:'abb')]
    public function templatetest (Request $request,ManagerRegistry $managerRegistry,EntityManagerInterface $entityManager,Security $security){
        if ($request->isMethod('POST')) {
            // Retrieve submitted verification code from the request
            $user = $security->getUser();
            // Faire quelque chose avec l'utilisateur connecté
            if ($user) {
                $submittedPassword = $request->request->get('confirm-password');
                $existingUser = $managerRegistry->getRepository(User::class)->findOneBy(['email' => $user->getEmail()]);
                $existingUser->setPassword(sha1($submittedPassword));
                $entityManager->flush();
//
                $this->addFlash('success', 'Mot de passe changé avec succes.');

            }


        }

        return $this->renderForm('user/securityProfile.html.twig'

        );

    }




    //------------------------------------------------------------
    //------------------------------------------------------------




    #[Route('/{id}/show', name: 'app_user_show', methods: ['GET'])]
    public function show(User $user): Response
    {
        return $this->render('user/show.html.twig', [
            'user' => $user,
        ]);
    }

//    #[Route('/{id}/edit', name: 'app_user_edit', methods: ['GET', 'POST','PUT'])]
//    public function edit(Request $request, User $user, EntityManagerInterface $entityManager): Response
//    {
//        $form = $this->createForm(UserModifType::class, $user, array(
//            'method'=>'put'
//));        $form->handleRequest($request);
//
//
//if ($form->isSubmitted() && $form->isValid()) {
//    dd('cbn');
//            $statut = $user->getStatut();
//            $datepunition = $user->getDatePunition();
//            if ($statut === 'desactive' && $datepunition <= new \DateTime()) {
//                // Si la validation échoue, ajoutez un message d'erreur
//                $this->addFlash('error', 'La date de punition doit être postérieure à la date actuelle pour desactiver le statut');
//                // Rediriger vers la page du formulaire avec le message d'erreur
//            }else{
//                if($user->getStatut()==='active'||$user->getStatut()==='ban' )
//                    $user->setDatepunition(new \DateTime('0000-00-00'));
////          if($user->getStatut()!='active'&& $user->getStatut()!='ban'&& $user->getStatut()!='desactive' )
////          { $user->setStatut("active");
////                $user->setDatepunition(new \DateTime('0000-00-00'));}
//
//                $entityManager->flush();
//
//            return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);}
//        }
//
//        return $this->renderForm('user/edit.html.twig', [
//            'user' => $user,
//            'form' => $form,
//        ]);
//    }
//    #[Route("/editUserJson/{id}", methods: ["POST"])]
//    public function editUser(Request $request, $id, UserRepository $userRepository, EntityManagerInterface $entityManager): JsonResponse
//    {
//        $user = $userRepository->find($id);
//        if (!$user) {
//            return new JsonResponse(['error' => 'User not found'], JsonResponse::HTTP_NOT_FOUND);
//        }
//
//        $statut = $request->request->get("statut");
//        $datepunition = $request->request->get("datepunition");
//
//        // Mettez à jour les propriétés de l'utilisateur
//        $user->setStatut($statut);
//        $user->setDatePunition(new \DateTime($datepunition));
//
//        // Validez les données, en fonction de vos besoins
//        // Exemple : vous pouvez ajouter des contraintes de validation pour le statut et la date de punition
//
//        // Enregistrez les modifications dans la base de données
//        $entityManager->flush();
//
//        // Renvoyez une réponse JSON avec les données mises à jour de l'utilisateur
//        return new JsonResponse($user);
//    }

//------------------Edit Profile--------------------------------------------------------
//--------------------------------------------------------------------------------------
    #[Route('/{id}/editp', name: 'app_user_edit_Profile', methods: ['GET', 'POST'])]
    public function editProfile(UserRepository $userRepository,Request $request, User $user, EntityManagerInterface $entityManager,SluggerInterface $slugger): Response
    {
        $user2 = $userRepository->findOneBy(["email" => $user->getEmail()]);

        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $image = $form->get('image')->getData();

            // this condition is needed because the 'brochure' field is not required
            // so the PDF file must be processed only when a file is uploaded
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
                    dd("Erreur");
                }

                $user->setImage($newFilename);

            }
            $entityManager->flush();
            return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('user/editProfile.html.twig', [
            'user' => $user2,
            'form' => $form,
        ]);
    }




//----------------------------------------------------------
//----------------------------Stripe-------------------------

    #[Route('/payer', name: 'app_user_payer', methods: ['GET', 'POST'])]
    public function payer(Request $request,CommandeRepository $commandeRepository ,Security $security,SessionInterface $session): Response
    {
        // Récupérer d'autres données du formulaire si nécessaire
        $choosePlan = $request->request->get('choosePlan');
        $prix = 0; // Prix par défaut
        $abonnement="abonnement";

        if ($choosePlan === 'exclusive') {
            $prix = 99;
        } else {
            $this->addFlash('error ', 'Il faut preciser votre choix.');
            return $this->renderForm('user/securityProfile.html.twig',[
            ]);        }
        $user_modif = [

            'prixx' => $prix,
        ];
        $session->set('commande', $user_modif);

        return $this->render('user/payer.html.twig', [
            'prix' => $prix,
            'user' => $security->getUser(),
            'intentSecret' => $commandeRepository->intentSecret($prix),
            'abonnement' =>$abonnement

            // Autres données à passer au modèle Twig si nécessaire
        ]);
    }
    #[Route('/payer/subscription', name: 'app_user_payer_subscription', methods: ['GET', 'POST'])]

    public function subscription( Request $request, CommandeRepository $commandeRepository,Security $security,EntityManagerInterface $em,SessionInterface $session){
        $user = $this->getUser();
        $user_modif = $session->get('commande');
        $prix = $user_modif['prixx'];
        $abonnement="abonnement";
        if($request->getMethod() === "POST") {
//            dd($commandeRepository->stripe($_POST, $prix,$abonnement));
            $resource = $commandeRepository->stripe($_POST, $prix,$abonnement);

            if(null !== $resource) {
                $commandeRepository->create_subscription($resource, $user,$prix,$em);

                return $this->render('user/responsePayement.html.twig', [
                    'prix' => $prix,
                    'abonnement' =>$abonnement
                ]);
            }
        }

        return $this->redirectToRoute('app_user_payer',
        );
    }
//----------------------------------------------------------
//<!----------------------------Stripe------------------------->




//    #[Route('/{id}', name: 'app_user_delete', methods: ['POST','GET'])]
//    public function delete(Request $request, User $user, EntityManagerInterface $entityManager): Response
//    {
//        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
//            $entityManager->remove($user);
//           // dd("dali");
//            $entityManager->flush();
//
//        }
//
//        return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
//    }

    #[Route('/changerPasswordProfile', name: 'mdpCh_profil_page')]
    public function passwordProfileChanger(Request $request,ManagerRegistry $managerRegistry,  EntityManagerInterface $entityManager ,Security $security )
    {



        // Comparer les codes de vérification
        if ($request->isMethod('POST')) {
            // Retrieve submitted verification code from the request
            //$user = $security->getUser();
            $user = new User();
            // Faire quelque chose avec l'utilisateur connecté
            if ($user) {
                $submittedPassword = $request->request->get('newPassword');
                $existingUser = $managerRegistry->getRepository(User::class)->findOneBy(['email' => $user->getEmail()]);
                $existingUser->setPassword(sha1($submittedPassword));
                $entityManager->flush();
//
                $this->addFlash('success', 'Mot de passe changé avec succes.');

            }
            else dd('mochekla');


        }

        return $this->renderForm('user/securityProfile.html.twig',[
        ]);


    }
    #[Route('/access-denied', name: 'access-denied')]

    public function accessDenied(): Response    {
        return $this->renderForm('user/nonAutorisé.html.twig',[
        ]);


    }

///////////////////////////////////////////////////
/// /////////////////////Methodes////////////////
    public function generateRecoveryCode(int $length): string {
        $characters = 'abcdefghijklmnopqrstuvwxyz';
        $charactersLength = strlen($characters);
        $recoveryCode = '';

        for ($i = 0; $i < $length; $i++) {
            $randomIndex = mt_rand(0, $charactersLength - 1);
            $recoveryCode .= $characters[$randomIndex];
        }

        return $recoveryCode;
    }

    public function envoyerMail(User $user)
    {


        $transport = Transport::fromDsn('smtp://finfoliofinfolio@gmail.com:txzoffvmvmoiuyzw@smtp.gmail.com:587');

// Create a Mailer object
        $mailer = new Mailer($transport);

// Create an Email object
        $email = (new Email());

// Set the "From address"
        $email->from('finfoliofinfolio@gmail.com');

// Set the "To address"
        $email->to($user->getEmail()
        );



// Set a "subject"
        $email->subject('Verification du compte!');

// Set the plain-text "Body"
        $email->text('The plain text version of the message.');

// Set HTML "Body"
        $email->html('<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vérification du compte</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 20px auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h2 {
            color: #333;
        }
        p {
            color: #666;
        }
        .verification-code {
            background-color: #f0f0f0;
            padding: 10px;
            text-align: center;
            border-radius: 5px;
            margin-top: 20px;
        }
        .footer {
            margin-top: 20px;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Vérification du compte</h2>
        <p>Nous vous remercions d"avoir choisi notre plateforme. Pour vérifier votre compte, veuillez utiliser le code suivant :</p>
        <div class="verification-code">
            <img src="cid:qrCode" alt="">

        </div>
        <p>Entrez ce code dans l\'application pour confirmer votre adresse e-mail et finaliser votre inGesscription.</p>
        <div class="footer">
            <p>Cordialement,<br>L\'équipe de FinFolio</p>
        </div>
    </div>
</body>
</html>');
        $email->embed(fopen('C:\Users\PC\Desktop\SymfonyFinFolio\public\assets\img\qrCode\codeQr.png', 'r'), 'qrCode');



// Sending email with status
        try {
            // Send email
            $mailer->send($email);

            // Display custom successful message
        } catch (TransportExceptionInterface $e) {
            // Display custom error message
            die('<style>* { font-size: 100px; color: #fff; background-color: #ff4e4e; }</style><pre><h1>&#128544;Error!</h1></pre>');

            // Display real errors
            # echo '<pre style="color: red;">', print_r($e, TRUE), '</pre>';
        }

    }



























    /*#[Route('/login', name: 'login')]
    public function login(Request $request ,UserRepository $userRepository): Response
    {

        $user = new User();
        $form = $this->createForm(UserLogin::class, $user, [
            'required' => false, // Désactive la validation automatique des champs vides
        ]);
        $form->handleRequest($request);
        $user2 = $userRepository->findOneBy(["email" => $user->getEmail()]);

        //dd($request);
        if ($form->isSubmitted() && $form->isValid()) {
           if ($user2){
               if ($user2->getPassword() == sha1($user->getPassword())) {
                   return $this->redirectToRoute('signup');

               } else {
                   return new JsonResponse("user found but pass wrong", 203);
               }
           }
            return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('user/login.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

*/
}

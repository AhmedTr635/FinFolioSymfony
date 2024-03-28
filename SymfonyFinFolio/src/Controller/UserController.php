<?php

namespace App\Controller;
use Symfony\Component\Form\FormError;
use App\Entity\User;
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
use Symfony\Component\HttpFoundation\JsonResponse;


#[Route('/user')]
class UserController extends AbstractController
{

    #[Route('/', name: 'app_user_index', methods: ['GET', 'POST'])]
    public function index(UserRepository $userRepository, Request $request, EntityManagerInterface $entityManager, ManagerRegistry $managerRegistry): Response
    {
        $user = new User();
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

            $user->setRate(2);
            $user->setNbcredit(0);
            $user->setRole("user");
            $user->setSolde(2000);
            $user->setRate(2);
            $user->setStatut("active");
            $user->setAdresse("Tunisie");
            $user->setPassword(sha1($user->getPassword()));
            $user->setDatepunition("vide");
            $user->setTotalTax(0);
            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('user/index.html.twig', [
            'users' => $userRepository->findAll(),
            'form' => $form->createView() // Passer la vue du formulaire au modèle Twig
        ]);
    }

    //------------------------SignUp------------------------------------
    //------------------------SignUp------------------------------------
    //------------------------SignUp------------------------------------
    //------------------------SignUp------------------------------------

    #[Route('/register', name: 'signup')]
    public function register(Request $request ,EntityManagerInterface $entityManager,ManagerRegistry $managerRegistry, SessionInterface $session): Response
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
                $code = $this->generateRecoveryCode(5);
                $this->envoyerMail($user, $code);
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
                $user->setDatepunition("vide");
                $user->setTotalTax(0);
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
    #[Route('/registerBack', name: 'signupback')]
    public function registerBack(Request $request ,EntityManagerInterface $entityManager,ManagerRegistry $managerRegistry): Response
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
                return $this->render('user/new.html.twig', [
                    'form' => $form->createView(),
                ]);
            }
            $user->setRate(2);
            $user->setNbcredit(0);
            $user->setRole("user");
            $user->setSolde(2000);
            $user->setRate(2);
            $user->setStatut("active");
            $user->setAdresse("Tunisie");
            $user->setPassword(sha1($user->getPassword()));
            $user->setDatepunition("vide");
            $user->setTotalTax(0);
            $entityManager->persist($user);
            $entityManager->flush();
        }

        return $this->renderForm('user/new.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    //------------------------Login------------------------------------
    //------------------------Login------------------------------------
    //------------------------Login------------------------------------
    //------------------------Login------------------------------------



    #[Route('/frontUser', name:'frontUser')]
    public function template (MailService $mailer){
        return $this->render('user/verification.html.twig');


    }
    #[Route('/listUsers', name:'ListUsers')]
    public function usersPage (){
        return $this->render('user/usersPage.html.twig');

    }




    //------------------------------------------------------------
    //------------------------------------------------------------




    #[Route('/{id}', name: 'app_user_show', methods: ['GET'])]
    public function show(User $user): Response
    {
        return $this->render('user/show.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_user_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('user/edit.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_user_delete', methods: ['POST'])]
    public function delete(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $entityManager->remove($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
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

    public function envoyerMail(User $user,string $code)
    {
        $htmlContent = '
<!DOCTYPE html>
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
        <p>Merci de vous être inscrit sur notre plateforme. Pour vérifier votre compte, veuillez utiliser le code suivant :</p>
        <div class="verification-code">
            <p style="font-size: 24px; font-weight: bold;">' . $code . '</p>
        </div>
        <p>Entrez ce code dans l\'application pour confirmer votre adresse e-mail et finaliser votre inscription.</p>
        <div class="footer">
            <p>Cordialement,<br>L\'équipe de FinFolio</p>
        </div>
    </div>
</body>
</html>
';
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
        $email->html($htmlContent);

// Add an "Attachment"
        /*
        $email->attachFromPath('example_1.txt');
        $email->attachFromPath('example_2.txt');

        // Add an "Image"
        $email->embed(fopen('image_1.png', 'r'), 'Image_Name_1');
        $email->embed(fopen('image_2.jpg', 'r'), 'Image_Name_2');*/

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

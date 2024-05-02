<?php
# src/Security/GoogleAuthenticator.php
namespace App\Security;

use App\Entity\User;
use App\Repository\UserRepository;
use League\OAuth2\Client\Provider\GoogleUser;
use Doctrine\ORM\EntityManagerInterface;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use KnpU\OAuth2ClientBundle\Security\Authenticator\OAuth2Authenticator;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twilio\Rest\Client;


class GoogleAuthenticator extends OAuth2Authenticator
{
    private ClientRegistry $clientRegistry;
    private EntityManagerInterface $entityManager;
    private RouterInterface $router;

    private UserRepository $usersRepository;
    private $accountSid;
    private $authToken;
    private $twilioPhoneNumber;
    private $twilioClient;
    private TokenStorageInterface $tokenStorage;
    public function __construct(private UrlGeneratorInterface $urlGenerator,ClientRegistry $clientRegistry, EntityManagerInterface $entityManager, RouterInterface $router,UserRepository $usersRepository,TokenStorageInterface $tokenStorage, string $accountSid, string $authToken, string $twilioPhoneNumber)
    {
        $this->clientRegistry = $clientRegistry;
        $this->entityManager = $entityManager;
        $this->router = $router;
        $this->usersRepository = $usersRepository;
        $this->tokenStorage = $tokenStorage;
        $this->accountSid = $accountSid;
        $this->authToken = $authToken;
        $this->twilioPhoneNumber = $twilioPhoneNumber;
        $this->twilioClient = new Client($accountSid, $authToken);
    }

    public function supports(Request $request): ?bool
    {
        // continue ONLY if the current ROUTE matches the check ROUTE
        return $request->attributes->get('_route') === 'connect_google_check';
    }
    function genererMotDePasse($string1, $string2) {
        $longueurMax = 10;
        $longueurMin = 8;
        $caracteresSpeciaux = array('!', '@', '#', '$', '%', '&');

        $motDePasse = '';

        // Concaténer les caractères des deux chaînes jusqu'à atteindre la longueur maximale
        foreach (str_split($string1.$string2) as $caractere) {
            if (strlen($motDePasse) < $longueurMax) {
                $motDePasse .= $caractere;
            }
        }

        // Ajouter des caractères aléatoires jusqu'à atteindre la longueur minimale
        while (strlen($motDePasse) < $longueurMin) {
            $motDePasse .= chr(rand(33, 126)); // Ajoute un caractère ASCII aléatoire
        }

        // Ajouter une majuscule si absente
        if (!preg_match('/[A-Z]/', $motDePasse)) {
            $motDePasse .= chr(rand(65, 90)); // Ajoute une majuscule ASCII aléatoire
        }

        // Ajouter un caractère spécial si absent
        if (!preg_match('/[!@#$%&]/', $motDePasse)) {
            $motDePasse .= $caracteresSpeciaux[rand(0, count($caracteresSpeciaux) - 1)];
        }

        return $motDePasse;
    }

    public function authenticate(Request $request): Passport
    {
        $client = $this->clientRegistry->getClient('google');
        $accessToken = $this->fetchAccessToken($client);

        return new SelfValidatingPassport(
            new UserBadge($accessToken->getToken(), function () use ($accessToken, $client) {
                /** @var GoogleUser $googleUser */
                $googleUser = $client->fetchUserFromToken($accessToken);
                $email = $googleUser->getEmail();

                $existingUser = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $email]);
 
                if (!$existingUser) {
                    $user = new User();
                    $password=$this->generatePassword();

                    $user->setEmail($googleUser->getEmail());
                    $user->setPrenom($googleUser->getFirstName());
                    $user->setNom($googleUser->getLastName());

                    $imageUrl = $googleUser->getAvatar();

                    $directory = 'C:\Users\PC\Desktop\SymfonyFinFolio\public\imagesUser';

                    $imageContent = file_get_contents($imageUrl);

                    if ($imageContent === false) {
                        echo "Erreur lors du téléchargement de l'image.";
                        exit;
                    }

                    $randomFileName = uniqid() . '_' . time() . '.jpg';

                    $localFilePath = $directory . '/' . $randomFileName;

                    $result = file_put_contents($localFilePath, $imageContent);

                    if ($result === false) {
                       dd("habetch tetsab");
                    }
                    $user->setImage($randomFileName);
                    $user->setPassword(sha1($password));
                    $user->setAdresse("Tunisie");
                    $user->setNumtel("+21653604045");
                    $user->setStatut("active");
                    $user->setNbcredit(0);
                    $user->setRole("user");
                    $user->setSolde(2000);
                    $user->setRate(2);
                    $user->setTypeCompte('simple');
                    $user->setDatepunition(new \DateTime('0000-00-00'));
                    $user->setTotalTax(0);
                    // $existingUser->setHostedDomain($googleUser->getHostedDomain());
                    $this->entityManager->persist($user);
                    $this->entityManager->flush();
                    $this->twilioClient->messages->create(
                "+21653604045",
                [
                    'from' => $this->twilioPhoneNumber,
                    'body' => "Ton mot de passe est ". $password
                ]
            );
                    return $user;
                }
//                $imageUrl = $googleUser->getAvatar();
//
//                $directory = 'C:\Users\PC\Desktop\SymfonyFinFolio\public\imagesUser';
//
//                $imageContent = file_get_contents($imageUrl);
//
//                if ($imageContent === false) {
//                    echo "Erreur lors du téléchargement de l'image.";
//                    exit;
//                }
//
//                $randomFileName = uniqid() . '_' . time() . '.jpg';
//
//                $localFilePath = $directory . '/' . $randomFileName;
//
//                $result = file_put_contents($localFilePath, $imageContent);
//
//                if ($result === false) {
//                    dd("habetch tetsab");
//                }
//                $existingUser->setImage($randomFileName);
//                $this->entityManager->flush();


                return $existingUser;
            })
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        $user = $token->getUser();
        $userIdentifier = $user->getUserIdentifier();
        $users = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $userIdentifier]);
        if ($users->getRole() === "user") {
            return new RedirectResponse($this->urlGenerator->generate('abb'));
        }
        if ($users->getRole() === "admin") {
            return new RedirectResponse($this->urlGenerator->generate('app_user_index'));
        }
        if ($targetPath = $this->getTargetPath($request->getSession(), $firewallName)) {
            return new RedirectResponse($targetPath);
        }

        // For example:
        return new RedirectResponse($this->urlGenerator->generate('app_login'));
        // change "app_dashboard" to some route in your app
        return new RedirectResponse(
            $this->router->generate('app_login')
        );

        // or, on success, let the request continue to be handled by the controller
        //return null;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        $message = strtr($exception->getMessageKey(), $exception->getMessageData());

        return new Response($message, Response::HTTP_FORBIDDEN);
    }
    function generatePassword() {
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()-_=+';
        $password = '';

        // Ajout d'une majuscule
        $password .= strtoupper($chars[rand(0, 25)]);

        // Ajout de 7 caractères aléatoires
        for ($i = 0; $i < 7; $i++) {
            $password .= $chars[rand(0, strlen($chars) - 1)];
        }

        // Mélanger les caractères pour rendre le mot de passe plus aléatoire
        $password = str_shuffle($password);

        return $password;
    }

//    public function start(Request $request, AuthenticationException $authException = null): Response
//    {
//        /*
//         * If you would like this class to control what happens when an anonymous user accesses a
//         * protected page (e.g. redirect to /login), uncomment this method and make this class
//         * implement Symfony\Component\Security\Http\EntryPoint\AuthenticationEntryPointInterface.
//         *
//         * For more details, see https://symfony.com/doc/current/security/experimental_authenticators.html#configuring-the-authentication-entry-point
//         */
//    }
}
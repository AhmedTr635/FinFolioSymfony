<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    /**
     * @Route("/users", name="user_list")
     */
    public function listUsers(): Response
    {
        // Fetch users from the database
        $users = $this->getDoctrine()->getRepository(User::class)->findAll();

        // Pass the users' data to the users.html.twig template
        return $this->render('users.html.twig', [
            'users' => $users,
        ]);
    }
}

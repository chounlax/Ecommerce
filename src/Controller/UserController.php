<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\UserRepository;

final class UserController extends AbstractController
{
    #[Route('/mod-liste-user', name: 'app_user')]
    public function index(UserRepository $userRepository): Response
    {
        $users = $userRepository->findAll();
        return $this->render('user/index.html.twig',[
            'users'=> $users
        ]);
    }

    #[Route('/private-profil', name: 'app_profil')]
    public function profil(): Response
    {
        return $this->render('user/profil.html.twig',[
        ]);
    }
}

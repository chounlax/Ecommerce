<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BaseController extends AbstractController
{
    #[Route('/', name: 'app_accueil')]
    public function index(): Response
    {
        return $this->render('base/index.html.twig');
    }

    #[Route('/contact', name: 'app_contact')]
    public function contact(): Response
    {
        return $this->render('base/contact.html.twig');
    }

    #[Route('/a-propos', name: 'app_apropos')]
    public function apropos(): Response
    {
        return $this->render('base/apropos.html.twig');
    }

    #[Route('/mentions-legales', name: 'app_mentions')]
    public function mentions(): Response
    {
        return $this->render('base/mentions.html.twig');
    }
}

<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\ProduitRepository;

class BaseController extends AbstractController
{
    #[Route('/', name: 'app_accueil')]
    public function index(): Response
    {
        return $this->render('base/index.html.twig');
    }

    #[Route('/mod-a-propos', name: 'app_apropos')]
    public function apropos(): Response
    {
        return $this->render('base/apropos.html.twig');
    }

    #[Route('/mentions-legales', name: 'app_mentions')]
    public function mentions(): Response
    {
        return $this->render('base/mentions.html.twig');
    }

    public function recherche(): Response
    {
        return $this->render('base/recherche.html.twig');
    }

    #[Route('/resultat', name: 'app_resultat')]
    public function resultat(Request $request, ProduitRepository $produitRepository): Response
    {
        $mot=null;
        $produits = [];
        if($request->isMethod('GET')){
            if($request->get("q")){
                $mot=$request->get("q");

                $produits=$produitRepository->recherche($mot);

            }
        }
        return $this->render('base/resultat.html.twig',
            ["produits" => $produits, "mot" => $mot]);
    }
}

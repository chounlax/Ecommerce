<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\Produit;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

final class FavorisController extends AbstractController
{
    #[Route('/private-favoris/{id}', name: 'app_favoris')]
    public function index(Produit $produit, EntityManagerInterface $em, Request $request): Response
    {
        $referer = $request->headers->get('referer');
        $u = $this->getUser();
        if ($u->getProduit()->contains($produit)) {
            $u->removeProduit($produit);
        } else {
            $u->addProduit($produit);
        }
        $em->persist($u);
        $em->flush();
        return $this->redirect($referer ?? $this->generateUrl("app_mes_produits"));
    }

    #[Route('/private-liste-favoris', name: 'app_liste_favoris')]
    public function listeFavoris(): Response
    {
        return $this->render('favoris/index.html.twig');
    }
}

<?php

namespace App\Controller;

use App\Entity\Ajouter;
use App\Entity\Panier;
use App\Entity\Produit;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class PanierController extends AbstractController
{
    #[Route('/private-panier/{id}', name: 'app_panier_add')]
    public function index(Request $request, Produit $produit, EntityManagerInterface $em): Response
    {
        $referer = $request->headers->get('referer');
        $u = $this->getUser();
        $panier = $u->getPanier();
        if (!$panier) {
            $panier = new Panier();
            $u->setPanier($panier);
        }
        $trouver = false;
        $ajouterTrouver = null;
        $total = $panier->getAjouters()->count();
        $i = 0;
        if ($total > 0) {
            do {
                $ajouter = $panier->getAjouters()->get($i);
                if ($ajouter->getProduits() == $produit) {
                    $trouver = true;
                    $ajouterTrouver = $ajouter;

                }
                $i++;
            } while (!$trouver && $i < $total);
        }
        /*foreach($panier->getAjouters() as $ajouter){

        if ($ajouter->getProduits() == $produit){
        $trouver = true;
        $ajouterTrouver = $ajouter;

        }
        }*/
        if ($trouver) {
            $ajouter = $ajouterTrouver;
            $ajouter->setQuantite($ajouter->getQuantite() + 1);
        } else {
            $ajouter = new Ajouter();
            $ajouter->setQuantite(1);
            $ajouter->setProduits($produit);
            $ajouter->setPaniers($panier);
        }

        $em->persist($u);
        $em->persist($panier);
        $em->persist($ajouter);
        $em->flush();
        return $this->redirect($referer ?? $this->generateUrl("app_mes_produits"));
    }

    #[Route('/private-liste-panier', name: 'app_panier')]
    public function apropos(): Response
    {
        return $this->render('panier/index.html.twig');
    }

    #[Route('/private-panier-moins/{id}', name: 'app_panier_moins')]
    public function moins(Request $request, Produit $produit, EntityManagerInterface $em): Response
    {
        $u = $this->getUser();
        $panier = $u->getPanier();
        if (!$panier) {
            $panier = new Panier();
            $u->setPanier($panier);
        }
        $trouver = false;
        $ajouterTrouver = null;
        $total = $panier->getAjouters()->count();
        $i = 0;
        if ($total > 0) {
            do {
                $ajouter = $panier->getAjouters()->get($i);
                if ($ajouter->getProduits() == $produit) {
                    $trouver = true;
                    $ajouterTrouver = $ajouter;

                }
                $i++;
            } while (!$trouver && $i < $total);
        }

        if ($trouver && $ajouterTrouver != null) {
            if($ajouterTrouver->getQuantite() > 1){
                $ajouter = $ajouterTrouver;
                $ajouter->setQuantite($ajouter->getQuantite() - 1);
                $em->persist($ajouter);
            }else{
                $em->remove($ajouter);
            }
        } 
        $em->persist($u);
        $em->persist($panier);
        $em->flush();
        return $this->render('panier/index.html.twig');
    }

    #[Route('/private-panier-supprimer/{id}', name: 'app_panier_supprimer')]
    public function supprimer(Request $request, Produit $produit, EntityManagerInterface $em): Response
    {
        $u = $this->getUser();
        $panier = $u->getPanier();
        if (!$panier) {
            $panier = new Panier();
            $u->setPanier($panier);
        }
        $trouver = false;
        $ajouterTrouver = null;
        $total = $panier->getAjouters()->count();
        $i = 0;
        if ($total > 0) {
            do {
                $ajouter = $panier->getAjouters()->get($i);
                if ($ajouter->getProduits() == $produit) {
                    $trouver = true;
                    $ajouterTrouver = $ajouter;

                }
                $i++;
            } while (!$trouver && $i < $total);
        }

        if ($trouver && $ajouterTrouver != null) {
            
                $em->remove($ajouter);
            
        } 
        $em->persist($u);
        $em->persist($panier);
        $em->flush();
        return $this->render('panier/index.html.twig');
    }

    #[Route('/private-panier-plus/{id}', name: 'app_panier_plus')]
public function plus(Produit $produit, EntityManagerInterface $em): Response
{
    $user = $this->getUser();
    $panier = $user->getPanier();

    // Si l'utilisateur n'a pas encore de panier
    if (!$panier) {
        $panier = new Panier();
        $user->setPanier($panier);
        $em->persist($panier);
    }

    $trouver = false;
    $ajouterTrouver = null;

    foreach ($panier->getAjouters() as $ajouter) {
        if ($ajouter->getProduits()->getId() === $produit->getId()) {
            $trouver = true;
            $ajouterTrouver = $ajouter;
            break;
        }
    }

    // 🟢 CAS 1 : le produit existe déjà → on augmente la quantité
    if ($trouver) {
        $quantite = $ajouterTrouver->getQuantite();
        $ajouterTrouver->setQuantite($quantite + 1);
    }
    // 🔵 CAS 2 : produit pas encore dans le panier → on crée la ligne
    else {
        $ajouter = new Ajouter();
        $ajouter->setProduits($produit);
        $ajouter->setQuantite(1);
        $ajouter->setPanier($panier);

        $em->persist($ajouter);
    }

    $em->flush();

    return $this->redirectToRoute('app_panier'); // redirige vers la page panier
}
}

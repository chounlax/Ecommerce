<?php

namespace App\Controller;

use App\Entity\Produit;
use App\Form\ModifierProduitType;
use App\Form\ProduitType;
use App\Form\SupprimerProduitType;
use App\Repository\ProduitRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ProduitController extends AbstractController
{
    #[Route('/produit', name: 'app_produit')]
    public function index(Request $request, EntityManagerInterface $em): Response
    {
        $produit = new Produit();
        $form = $this->createForm(ProduitType::class, $produit);

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $em->persist($produit);
                $em->flush();

                $this->addFlash('notice', 'Message envoyé');
                return $this->redirectToRoute('app_produit');
            }
        }

        return $this->render('produit/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/liste_produits', name: 'app_liste_produits')]
    public function contact(ProduitRepository $produitRepository,Request $request
    ,EntityManagerInterface $em
    ): Response
    {
        $produits = $produitRepository->findAll();
        $form = $this->createForm(SupprimerProduitType::class, null, [
            'produits' => $produits,
        ]);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $selectedProduits = $form->get('produits')->getData();
            foreach ($selectedProduits as $produit) {
                $em->remove($produit);
            }
            $em->flush();

            $this->addFlash('notice', 'Produits Supprimés');
            return $this->redirectToRoute('app_liste_produits');

        }

        return $this->render('produit/liste-produits.html.twig', [
            'produits' => $produits,
            'form' => $form->createView(),
        ]);

    }

    #[Route('/modifier-produit{id}', name: 'app_modifier_produit')]

    public function modifierCategorie(Request $request, EntityManagerInterface $em, Produit $produit): Response
    {
        $form = $this->createForm(ModifierProduitType::class, $produit);
        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $em->persist($produit);
                $em->flush();
                $this->addFlash('notice', 'Produit modifié');
                return $this->redirectToRoute('app_liste_produits');
            }
        }

        return $this->render('produit/modifier-produit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/supprimer-produit/{id}', name: 'app_supprimer_produit')]
    public function supprimerCategorie(Request $request, Produit $produit, EntityManagerInterface $em): Response
    {
        if ($produit != null) {
            $em->remove($produit);
            $em->flush();
            $this->addFlash('notice', 'Produit supprimé');
        }
        return $this->redirectToRoute('app_liste_produits');
    }

}

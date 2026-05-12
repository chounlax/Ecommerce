<?php

namespace App\Controller;

use App\Entity\Commande;

use App\Repository\ProduitRepository;
use App\Repository\AjouterRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class CommandeController extends AbstractController
{
    #[Route('/commande', name: 'app_commande')]
    public function valider(EntityManagerInterface $em): Response 
{
    // 1. Récupérer l'utilisateur
    $user = $this->getUser();
    if (!$user) {
        return $this->redirectToRoute('app_login');
    }

    // 2. Récupérer son panier (via la relation sur ton schéma)
    $panier = $user->getPanier(); // Vérifie que tu as bien cette méthode dans ton entité User

    if (!$panier || $panier->getAjouters()->isEmpty()) {
        $this->addFlash('warning', 'Votre panier est vide.');
        return $this->redirectToRoute('app_mes_produits');
    }

    $listeProduits = [];
    $totalGeneral = 0;

    // 3. Parcourir les lignes de la table "ajouter" liées à ce panier
    // Dans ton entité Panier, la relation vers Ajouter s'appelle probablement 'ajouters'
    foreach ($panier->getAjouters() as $ligne) {
        $produit = $ligne->getProduits(); // Selon ton schéma : produits_id
        $quantite = $ligne->getQuantite();
        
        $prixCourant = $produit->getPrix();
        $sousTotal = $prixCourant * $quantite;
        $totalGeneral += $sousTotal;

        $listeProduits[] = [
            'nom' => $produit->getNom(),
            'prix' => $prixCourant,
            'quantite' => $quantite,
            'total_ligne' => $sousTotal
        ];

        // 4. On prépare la suppression de la ligne "ajouter"
        $em->remove($ligne);
    }

    // 5. Créer la commande finale
    $commande = new Commande();
    $commande->setIdUser($user);
    $commande->setProduits($listeProduits); // Ton tableau PHP
    $commande->setprixTotal($totalGeneral);
    $commande->setDateAchat(new \DateTime('now', new \DateTimeZone('Europe/Paris')));
   

    $em->persist($commande);
    
    // 6. Exécuter la sauvegarde et le nettoyage du panier
    $em->flush();

    $this->addFlash('success', 'Commande validée avec succès !');

    return $this->redirectToRoute('app_profil');
}
}

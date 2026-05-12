<?php

namespace App\Repository;

use App\Entity\Produit;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Produit>
 */
class ProduitRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Produit::class);
    }

    /**
     * @return Produit[] Returns an array of Produit objects
     */
    public function recherche($value, $sort, $categories): array
    {
        $qb = $this->createQueryBuilder('p');

        // Recherche par mot-clé
        if (!empty($value)) {
            $qb->andWhere('p.description LIKE :val OR p.nom LIKE :val')
                ->setParameter('val', '%' . $value . '%');
        }

        // Filtrage par catégories (tableau d'IDs)
        if (!empty($categories)) {
            $qb->andWhere('p.categorie IN (:cats)')
                ->setParameter('cats', $categories);
        }

        // Gestion du tri dynamique
        switch ($sort) {
            // Tu peux ajouter des cas selon tes besoins
            case 'price_asc':
                $qb->orderBy('p.prix', 'ASC');
                break;
            case 'price_desc':
                $qb->orderBy('p.prix', 'DESC');
                break;
            default:
                $qb->orderBy('p.nom', 'ASC'); // Tri par défaut
                break;
        }

        return $qb->getQuery()->getResult();
    }

    public function findAllCategories(): array
    {
        return $this->createQueryBuilder('p')
            ->select('p.categorie')
            ->distinct()
            ->where('p.categorie IS NOT NULL')
            ->orderBy('p.categorie', 'ASC')
            ->getQuery()
            ->getResult();
    }

    //    public function findOneBySomeField($value): ?Produit
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}

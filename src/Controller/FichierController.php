<?php

namespace App\Controller;

use App\Entity\Fichier;
use App\Form\FichierType;
use App\Repository\FichierRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

final class FichierController extends AbstractController
{

    #[Route('/private-fichier', name: 'app_fichier')]
    public function fichier(Request $request, EntityManagerInterface $em, SluggerInterface $slugger): Response
    {
        $fichier = new Fichier();
        $form = $this->createForm(FichierType::class, $fichier);

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {

                $file = $form->get('fichier')->getData();

                if ($file) {
                    $nomFichierServeur = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                    $nomFichierServeur = $slugger->slug($nomFichierServeur);
                    $nomFichierServeur = $nomFichierServeur . '-' . uniqid() . '.' . $file->guessExtension();
                    try {
                        $fichier->setNomServeur($nomFichierServeur);
                        $fichier->setNomOriginal($file->getClientOriginalName());
                        $fichier->setDateEnvoi(new \DateTime('now', new \DateTimeZone('Europe/Paris')));
                        $fichier->setExtension($file->guessExtension());
                        $fichier->setTaille($file->getSize());
                        $em->persist($fichier);
                        $em->flush();
                        $file->move($this->getParameter('file_directory'), $nomFichierServeur);
                        $this->addFlash('notice', 'Fichier envoyé');
                        return $this->redirectToRoute('app_fichier');
                    } catch (FileException $e) {
                        $this->addFlash('notice', 'Erreur d\'envoi');
                    }
                }
            }
        }

        return $this->render('fichier/index.html.twig', [
            'form' => $form->createView(),
        ]);

    }

    #[Route('/mod-liste-fichier', name: 'app_liste-fichier')]
    public function index(FichierRepository $fichierRepository): Response
    {
        $fichiers = $fichierRepository->findAll();
        return $this->render('fichier/liste-fichier.html.twig', [
            'fichiers' => $fichiers,
        ]);
    }
}

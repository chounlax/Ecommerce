<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/utilisateurs')]
#[IsGranted('ROLE_ADMIN')] // Seul un admin peut accéder à cette page
class AdminUserController extends AbstractController
{
    #[Route('/', name: 'app_admin_users')]
    public function index(UserRepository $userRepository): Response
    {
        return $this->render('admin_user/index.html.twig', [
            'users' => $userRepository->findAll(),
        ]);
    }

    #[Route('/modifier-role/{id}', name: 'app_admin_update_role', methods: ['POST'])]
    public function updateRole(User $user, Request $request, EntityManagerInterface $em): Response
    {
        $newRole = $request->request->get('role');
        
        if ($newRole) {
            // Symfony attend un tableau pour les rôles
            $user->setRoles([$newRole]);
            $em->flush();
            $this->addFlash('success', 'Le rôle de ' . $user->getEmail() . ' a été mis à jour.');
        }

        return $this->redirectToRoute('app_admin_users');
    }
    #[Route('/MCD', name: 'app_admin_mcd')]
    public function mcd(): Response
    {
        return $this->render('admin_user/mcd.html.twig');
    }
}
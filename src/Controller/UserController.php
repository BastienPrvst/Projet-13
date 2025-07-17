<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserRegisterForm;
use App\Repository\OrderRepository;
use Symfony\Component\HttpFoundation\Session\Session;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

final class UserController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function register(Request $request,UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $em): Response
    {
        $user = new User();
        $form = $this->createForm(UserRegisterForm::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $hashedPassword = $passwordHasher->hashPassword($user, $user->getPassword());
            $user->setPassword($hashedPassword);
            $em->persist($user);
            $em->flush();
            return $this->redirectToRoute('app_login');
        }

        return $this->render('register.html.twig', [
            'form' => $form,
        ]);

    }

    #[Route(path: '/mon-compte', name: 'app_profile')]
    public function showProfile(OrderRepository $orderRepository ): Response
    {
        /* @var User $user */
        $user = $this->getUser();
        $userOrders = $orderRepository->findBy([
            'client' => $user
        ]);
        return $this->render('profile.html.twig', [
            'success' => null,
            'userOrders' => $userOrders
        ]);

    }

    #[Route(path: '/supprimer-mon-compte', name: 'app_delete_account')]
    public function deleteAccount(EntityManagerInterface $em): Response
    {
        /* @var User $user */
        $user = $this->getUser();
        $em->remove($user);
        $em->flush();

        $session = new Session();
        $session->invalidate();

        return $this->redirectToRoute('app_home');
    }

    #[Route(path: '/activer-api', name: 'app_enable_api')]
    public function enableApi(EntityManagerInterface $em): Response
    {
        /* @var User $user */
        $user = $this->getUser();
        $roles = $user->getRoles();
        if(!in_array('ROLE_API_USER', $roles, true)){
            $roles[] = 'ROLE_API_USER';
            $this->addFlash('success', 'Votre accès à l\'API a bien été enregistré.');
            $user->setRoles($roles);
            $em->persist($user);
            $em->flush();
        }else{
            $roles = array_filter($roles, static fn ($role) => $role !== 'ROLE_API_USER');
            $this->addFlash('success', 'Votre accès à l\'API a bien été désactivé.');
            $user->setRoles($roles);
            $em->persist($user);
            $em->flush();

        }

        return $this->redirectToRoute('app_login');
    }


}

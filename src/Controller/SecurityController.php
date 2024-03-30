<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    #[Route('/register', name: 'register')]
    public function register(Request $request, UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $em): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $form->getData();
            $user->setRoles(['ROLE_USER']);
            $password = $passwordHasher->hashPassword($user, $user->getPassword());
            $user->setPassword($password);

            $em->persist($user);
            $em->flush();

            return new RedirectResponse($this->generateUrl('admin_main_page'));
        }

        return $this->render('front/register.html.twig', [
            'form' => $form->createView()
        ]);
    }




    #[Route('/login', name: 'login')]
    public function login(AuthenticationUtils $helper): Response
    {
        return $this->render('front/login.html.twig', [
            'error' => $helper->getLastAuthenticationError()
        ]);
    }


    #[Route('/logout', name: 'logout')]
    public function logout(): void
    {
        throw new \Exception('This should never be reached!');
    }
}

<?php

namespace App\Controller;

use App\Controller\Traits\SaveSubscription;
use App\Entity\Subscription;
use App\Entity\User;
use App\Form\UserType;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

class SecurityController extends AbstractController
{
    use SaveSubscription;

    private $tokenStorage;
    private $eventDispatcher;

    public function __construct(TokenStorageInterface $tokenStorage, EventDispatcherInterface $eventDispatcher)
    {
        $this->tokenStorage = $tokenStorage;
        $this->eventDispatcher = $eventDispatcher;
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

    #[Route('/register/{plan}', name: 'register', defaults: ['plan' => null])]
    public function register(Request $request, UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $em, SessionInterface $session, $plan): Response
    {
        if ($request->isMethod('GET')) {
            $session->set('planName', $plan);
            $session->set('planPrice', Subscription::getPlanDataPriceByName($plan));
        }

        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $form->getData();
            $password = $passwordHasher->hashPassword($user, $user->getPassword());
            $user->setPassword($password);
            $user->setRoles(['ROLE_USER']);

            $date = new DateTime();
            $date->modify('+1 month');
            $subscription = new Subscription();
            $subscription->setValidTo($date);
            $subscription->setPlan($session->get('planName'));
            if ($plan == Subscription::getPlanDataNameByIndex(0)) {
                $subscription->setFreePlanUsed(true);
                $subscription->setPaymentStatus('paid');
            }
            $user->setSubscription($subscription);

            $em->persist($user);
            $em->flush();

            $this->loginUserAutomatically($user, $request);

            return $this->redirectToRoute('admin_main_page');
        }
        if ($this->isGranted('IS_AUTHENTICATED_REMEMBERED') && $plan == Subscription::getPlanDataNameByIndex(0)) {
            $this->saveSubscription($plan, $this->getUser());

            return $this->redirectToRoute('admin_main_page');
        } elseif ($this->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            return $this->redirectToRoute('payment');
        }
        return $this->render('front/register.html.twig', [
            'form' => $form->createView()
        ]);
    }

    private function loginUserAutomatically($user, $request)
    {
        $userToken = new UsernamePasswordToken($user, 'main', $user->getRoles());
            $this->tokenStorage->setToken($userToken);

            // Fire the login event
            $event = new InteractiveLoginEvent($request, $userToken);
            $this->eventDispatcher->dispatch($event);
    }
}

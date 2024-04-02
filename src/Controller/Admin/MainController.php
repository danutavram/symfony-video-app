<?php

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Utils\CategoryTreeAdminOptionList;
use App\Entity\Video;
use App\Entity\User;
use App\Form\UserType;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[Route('/admin')]
class MainController extends AbstractController
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }
    #[Route('/', name: 'admin_main_page')]
    public function index(Request $request, UserPasswordHasherInterface $passwordHasher): Response
    {
        $user = $this->getUser();
        $form = $this->createForm(UserType::class, $user, ['user' => $user]);
        $form->handleRequest($request);
        $is_invalid = null;

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $form->getData();
            $password = $passwordHasher->hashPassword($user, $user->getPassword());
            $user->setPassword($password);

            $this->em->flush();

            $this->addFlash('success', 'Your changes were saved!');

            return $this->redirectToRoute('admin_main_page');
        } elseif ($request->isMethod('POST')) {
            $is_invalid = 'is-invalid';
        }
        $user = $this->getUser();
        $subscription = $user instanceof User ? $user->getSubscription() : null;

        return $this->render('admin/my_profile.html.twig', [
            'subscription' => $subscription,
            'form' => $form->createView(),
            'is_invalid' => $is_invalid
        ]);
    }

    #[Route('/delete_account', name: 'delete_account')]
    public function deleteAccount()
    {
        $user = $this->em->getRepository(User::class)->find($this->getUser());

        $this->em->remove($user);
        $this->em->flush();

        session_destroy();

        return $this->redirectToRoute('main_page');
    }

    #[Route('/cancel-plan', name: 'cancel_plan')]
    public function cancelPlan()
    {
        $user = $this->em->getRepository(User::class)->find($this->getUser());

        $subscription = $user->getSubscription();
        $subscription->setValidTo(new DateTime());
        $subscription->setPaymentStatus(null);
        $subscription->setPlan('canceled');

        $this->em->persist($user);
        $this->em->persist($subscription);
        $this->em->flush();

        return $this->redirectToRoute('admin_main_page');
    }

    #[Route('/videos', name: 'videos')]
    public function videos(CategoryTreeAdminOptionList $categories): Response
    {
        if ($this->isGranted('ROLE_ADMIN')) {
            $categories->getCategoryList($categories->buildTree());
            $videos = $this->em->getRepository(Video::class)->findBy([], ['title' => 'ASC']);
        } else {
            $categories = null;
            $user = $this->getUser();

            if ($user instanceof User && method_exists($user, 'getLikedVideos')) {
                $categories = null;
                $videos = $user->getLikedVideos();
            } else {
                $videos = $this->em->getRepository(Video::class)->findBy([], ['title' => 'ASC']);
            }
        }

        return $this->render('admin/videos.html.twig', [
            'videos' => $videos,
            'categories' => $categories
        ]);
    }
}

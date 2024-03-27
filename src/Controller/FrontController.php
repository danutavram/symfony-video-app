<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Comment;
use App\Entity\User;
use App\Entity\Video;
use App\Form\UserType;
use App\Repository\VideoRepository;
use App\Utils\CategoryTreeFrontPage;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class FrontController extends AbstractController
{

    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    #[Route('/', name: 'main_page')]
    public function index(): Response
    {
        return $this->render('front/index.html.twig');
    }

    #[Route('/video-list/category/{categoryname},{id}/{page}', defaults: ['page' => '1'], name: 'video_list')]
    public function videoList($id, $page, CategoryTreeFrontPage $categories, Request $request): Response
    {
        $categories->getCategoryListAndParent($id);
        $ids = $categories->getChildIds($id);
        array_push($ids, $id);
        $videos = $this->em->getRepository(Video::class)->findByChildIds($ids, $page, $request->get('sortby'));
        return $this->render('front/video_list.html.twig', [
            'subcategories' => $categories,
            'videos' => $videos
        ]);
    }

    #[Route('/video-details/{video}', name: 'video_details')]
    public function videoDetails($video, VideoRepository $repo): Response
    {
        return $this->render('front/video_details.html.twig', [
            'video'=> $repo->videoDetails($video)
        ]);
    }

    #[Route('/search-results/{page}', methods: ['GET'], defaults:['page' => '1'], name: 'search_results')]
    public function searchResults($page, Request $request): Response
    {
        $videos = null;
        $query = null;

        if($query = $request->get('query')) {
            $videos = $this->em->getRepository(Video::class)->findByTitle($query, $page, $request->get('sortby'));

            if(!$videos->getItems()) $videos = null;
        }
        return $this->render('front/search_results.html.twig', [
            'videos' => $videos,
            'query' => $query
        ]);
    }

    #[Route('/pricing', name: 'pricing')]
    public function pricing(): Response
    {
        return $this->render('front/pricing.html.twig');
    }



    #[Route('/register', name: 'register')]
    public function register(Request $request, UserPasswordHasherInterface $passwordHasher, SessionInterface $session): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) { 
            $user = $form->getData();
            $user->setRoles(['ROLE_USER']);
            $password = $passwordHasher->hashPassword($user, $user->getPassword());
            $user->setPassword($password);
    
            $this->em->persist($user);
            $this->em->flush();
    
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

    #[Route('/payment', name: 'payment')]
    public function payment(): Response
    {
        return $this->render('front/payment.html.twig');
    }

    #[Route(path: '/new-comment/{video}', name: 'new_comment', methods: ['POST'])]
    public function newComment(Video $video, Request $request)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_REMEMBERED');
        if (!empty(trim($request->request->get('comment')))) {
            $comment = new Comment;
            $comment->setContent($request->request->get('comment'));
            $comment->setUser($this->getUser());
            $comment->setVideo($video);
            
            $this->em->persist($comment);
            $this->em->flush();
        }
        return $this->redirectToRoute('video_details', [
            'video'=>$video->getId()
        ]);
    }

    public function mainCategories()
    {
        $categories = $this->em
            ->getRepository(Category::class)
            ->findBy(['parent' => null], ['name' => 'ASC']);
        return $this->render('front/_main_categories.html.twig', [
            'categories' => $categories
        ]);
    }
}

<?php

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Utils\CategoryTreeAdminOptionList;
use App\Entity\Video;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

#[Route('/admin')]
class MainController extends AbstractController
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }
    #[Route('/', name: 'admin_main_page')]
    public function index(): Response
    {
        return $this->render('admin/my_profile.html.twig');
    }
    #[Route('/videos', name: 'videos')]
    public function videos(): Response
    {
        if ($this->isGranted('ROLE_ADMIN')) {
            $videos = $this->em->getRepository(Video::class)->findAll();
        } else {
            $user = $this->getUser();

            if ($user instanceof User && method_exists($user, 'getLikedVideos')) {
                $videos = $user->getLikedVideos();
            } else {
                $videos = $this->em->getRepository(Video::class)->findAll();
            }
        }

        return $this->render('admin/videos.html.twig', [
            'videos' => $videos
        ]);
    }
    public function getAllCategories(CategoryTreeAdminOptionList $categories, $editedCategory = null)
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $categories->getCategoryList($categories->buildTree());
        return $this->render('admin/_all_categories.html.twig', [
            'categories' => $categories,
            'editedCategory' => $editedCategory
        ]);
    }
}

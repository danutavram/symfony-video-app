<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Utils\CategoryTreeAdminList;
use App\Utils\CategoryTreeAdminOptionList;
use Symfony\Component\HttpFoundation\Request;

#[Route('/admin')]

class AdminController extends AbstractController
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

    #[Route('/categories', name: 'categories', methods:['GET', 'POST'])]
    public function categories(CategoryTreeAdminList $categories, Request $request): Response
    {
        $categories->getCategoryList($categories->buildTree());

        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            dd('valid');
        }

        return $this->render('admin/categories.html.twig', [
            'categories' => $categories->categorylist,
            'form'=>$form->createView()
        ]);
    }

    #[Route('/edit-category/{id}', name: 'edit_category')]
    public function editCategory(Category $category): Response
    {
        return $this->render('admin/edit_category.html.twig', [
            'category' => $category
        ]);
    }

    #[Route('/delete-category/{id}', name: 'delete_category')]
    public function deleteCategory(Category $category): Response
    {
        $this->em->remove($category);
        $this->em->flush();
        return $this->redirectToRoute('categories');
    }

    #[Route('/videos', name: 'videos')]
    public function videos(): Response
    {
        return $this->render('admin/videos.html.twig');
    }

    #[Route('/upload-video', name: 'upload_video')]
    public function uploadVideo(): Response
    {
        return $this->render('admin/upload_video.html.twig');
    }

    #[Route('/users', name: 'users')]
    public function users(): Response
    {
        return $this->render('admin/users.html.twig');
    }

    public function getAllCategories(CategoryTreeAdminOptionList $categories, $editedCategory = null)
    {
        $categories->getCategoryList($categories->buildTree());
        return $this->render('admin/_all_categories.html.twig', [
            'categories' => $categories,
            'editedCategory' => $editedCategory
        ]);
    }
}

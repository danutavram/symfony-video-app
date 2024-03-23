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
use Symfony\Component\HttpFoundation\Session\SessionInterface;

#[Route('/admin')]

class AdminController extends AbstractController
{
    private $em;
    private $session;

    public function __construct(EntityManagerInterface $em, SessionInterface $session)
    {
        $this->em = $em;
        $this->session = $session;
    }

    #[Route('/', name: 'admin_main_page')]
    public function index(): Response
    {
        return $this->render('admin/my_profile.html.twig');
    }

    #[Route('/su/categories', name: 'categories', methods:['GET', 'POST'])]
    public function categories(CategoryTreeAdminList $categories, Request $request): Response
    {
        $categories->getCategoryList($categories->buildTree());
    
        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category);
        $is_invalid = null;
    
        if ($this->saveCategory($category, $form, $request)) {
            
            return $this->redirectToRoute('categories');
        } elseif ($request->isMethod('POST')) {
            $is_invalid = ' is-invalid';
        }
    
        return $this->render('admin/categories.html.twig', [
            'categories' => $categories->categorylist,
            'form' => $form->createView(),
            'is_invalid' => $is_invalid,
            'category'=>$category
        ]);
    }
    
    #[Route('/su/edit-category/{id}', name: 'edit_category', methods:['GET', 'POST'])]
    public function editCategory(Category $category, Request $request): Response
    {

        $form = $this->createForm(CategoryType::class, $category);
        $is_invalid = null;

        if ($this->saveCategory($category, $form, $request)) {
            
            return $this->redirectToRoute('categories');
        } elseif ($request->isMethod('POST')) {
            $is_invalid = ' is-invalid';
        }

        return $this->render('admin/edit_category.html.twig', [
            'category' => $category,
            'form' => $form->createView(),
            'is_invalid'=> $is_invalid
        ]);
    }

    #[Route('/su/delete-category/{id}', name: 'delete_category')]
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

    #[Route('/su/upload-video', name: 'upload_video')]
    public function uploadVideo(): Response
    {
        return $this->render('admin/upload_video.html.twig');
    }

    #[Route('/su/users', name: 'users')]
    public function users(): Response
    {
        return $this->render('admin/users.html.twig');
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

    private function saveCategory($category, $form, $request)
    {
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            $categoryData = $form->getData(); // Obținem datele formularului
    
            $name = $categoryData->getName();
            $parent = $categoryData->getParent();
    
            $category->setName($name);
    
            if ($parent instanceof Category) {
                $category->setParent($parent);
            }
    
            $this->em->persist($category);
            $this->em->flush();

            return true;
    }
    return false;
}
}
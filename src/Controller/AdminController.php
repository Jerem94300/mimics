<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryFormType;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\Console\EntityManagerProvider;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

final class AdminController extends AbstractController
{
    #[Route('/admin', name: 'app_admin')]
    public function admin(): Response
    {
        return $this->render('admin/index.html.twig', []);
    }

    #[Route('/admin/products', name: 'app_admin_products')]
    public function adminProducts(): Response
    {
        return $this->render('admin/products.html.twig', []);
    }

    #[Route('/admin/category', name: 'app_admin_category')]
    public function adminCategory(Request $request, EntityManagerInterface $entityManager, CategoryRepository $repoCategory): Response

    {
        $category = new Category;

        $form = $this->createForm(CategoryFormType::class, $category);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $category->setCreatedAt((new \DateTimeImmutable()));

            $entityManager->persist(($category));

            $entityManager->flush();

            // dump($request);
            // dump($category);
            // Message utilsateur stockés en session
            $this->addFlash('success', "La catégorie a été ajoutée");

            return $this->redirectToRoute('app_admin_category');
        }
        // Une classe Repository contient des methodes permettant uniquement d'executer des requetes de selections (SELECT) en BDD (find($id), findAll(), findBy(), findOneBy() )

        $dbCategory = $repoCategory->findAll();
        // dump($dbCategory);

        return $this->render('admin/category.html.twig', [
            'categoryForm' => $form,
            'dbCategory' => $dbCategory
        ]);
    }

    #[Route('/admin/category/update/{id}', name: 'app_admin_category_update')]
    public function adminCategoryUpdate($id, Category $category, Request $request, EntityManagerInterface $entityManager, CategoryRepository $repoCategory): Response
    {
        // dump($category);

        //SELECT * FROM category WHERE id = $id + fetch(PDO::FETCH_ASSOC)
        $category = $repoCategory->find($id);
        // dump($id);
        // dump($category);

        $form = $this->createForm(CategoryFormType::class, $category);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager->persist($category);
            $entityManager->flush();

            dump($category->getTitle());
            $categoryTitle = $category->getTitle();

            $this->addFlash('success', "La catégorie <strong class='text-white'>$categoryTitle</strong> a été modifiée.");

            // return $this->redirectToRoute('app_admin_category');
        }

        $dbCategory = $repoCategory->findAll();

        return $this->render('admin/category.html.twig', [
            'categoryForm' => $form,
            'dbCategory' => $dbCategory
        ]);
    }

    #[Route('/admin/category/remove{id}', name: 'app_admin_category_remove')]
    public function adminCategoryRemove(EntityManagerInterface $entityManager, CategoryRepository $repoCategory)
    {
        // return $this->render('admin/orders.html.twig', []);
    }



    #[Route('/admin/orders', name: 'app_admin_orders')]
    public function adminOrders(): Response
    {
        return $this->render('admin/orders.html.twig', []);
    }

    #[Route('/admin/users', name: 'app_admin_users')]
    public function adminUsers(): Response
    {
        return $this->render('admin/users.html.twig', []);
    }
}

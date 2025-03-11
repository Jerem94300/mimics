<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Product;
use App\Form\CategoryFormType;
use App\Form\ProductFormType;
use App\Repository\CategoryRepository;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\Console\EntityManagerProvider;
use PhpParser\Node\Stmt\TryCatch;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\String\Slugger\SluggerInterface;

final class AdminController extends AbstractController
{
    #[Route('/admin', name: 'app_admin')]
    public function admin(): Response
    {
        return $this->render('admin/index.html.twig', []);
    }

    #[Route('/admin/products', name: 'app_admin_products')]
    #[Route('/admin/product/update/{id}', name: 'app_admin_product_update')]

    // ?Product $product : le ? veut dire que par defaut $product a une valeur null
    public function adminProducts(?Product $product, Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger, ProductRepository $repoProduct): Response
    {
        dump($product);
        if (!$product) {
            $product = new Product;
        }


        $form = $this->createForm(ProductFormType::class, $product);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $pictureFile = $form->get('picture')->getData();
            // dump($pictureFile);


            if ($pictureFile) {
                //retourne le nom du fichier d'origine sans l'extension
                $originalFileName = pathinfo($pictureFile->getClientOriginalName(), PATHINFO_FILENAME);
                dump($originalFileName);
                //slug() sécurise le nom du fichier (supression des espace etc..)
                $safeFileName = $slugger->slug($originalFileName);
                // dump($safeFileName);
                // on renomme l'image
                $newFileName = $safeFileName . '-' . uniqid() . '.' . $pictureFile->guessExtension();
                // dump($newFileName);
                // dump($this->getParameter('image_directory'));
                $currentPath = $this->getParameter('image_directory');

                try {
                    $pictureFile->move($currentPath, $newFileName);
                } catch (FileException $e) {
                    dump($e);
                }

                $product->setPicture($newFileName);

                dump($product);
            }

            $product->setCreateAt(new \DateTimeImmutable());
            $entityManager->persist($product);
            $entityManager->flush();

            $this->addFlash('success', "Le produit a été ajouté");

            return $this->redirectToRoute('app_admin_products');
        }

        // Exo selectionner les produits, transmettre au template et afficher les données dans product.html



        $dbProduct = $repoProduct->findAll();
        dump($dbProduct);



        return $this->render('admin/products.html.twig', [
            'productForm' => $form,
            'dbProduct' => $dbProduct,
            'pictureFile' => $product->getPicture()
        ]);
    }


    // #[Route('/admin/product/update/{id}', name: 'app_admin_product_update')]
    // public function adminProductUpdate($id, Product $product, Request $request, EntityManagerInterface $entityManager, ProductRepository $repoProduct): Response
    // {
    //     // dump($category);

    //     //SELECT * FROM category WHERE id = $id + fetch(PDO::FETCH_ASSOC)
    //     $product = $repoProduct->find($id);
    //     // dump($id);
    //     // dump($category);

    //     $form = $this->createForm(ProductFormType::class, $product);

    //     $form->handleRequest($request);

    //     if ($form->isSubmitted() && $form->isValid()) {

    //         $entityManager->persist($product);
    //         $entityManager->flush();

    //         dump($product->getTitle());
    //         $productTitle = $product->getTitle();

    //         $this->addFlash('success', "Le produit <strong class='text-white'>$productTitle</strong> a été modifiée.");

    //         // return $this->redirectToRoute('app_admin_category');
    //     }

    //     $dbProduct = $repoProduct->findAll();

    //     return $this->render('admin/products.html.twig', [
    //         'productForm' => $form,
    //         'dbProduct' => $dbProduct
    //     ]);
    // }


    #[Route('/admin/product/remove{id}', name: 'app_admin_product_remove')]
    public function adminProductRemove($id, EntityManagerInterface $entityManager, ProductRepository $repoProduct)
    {

        $product = $repoProduct->find($id);
        // dump($product);

        $productTitle = $product->getTitle();


        $entityManager->remove($product);
        $entityManager->flush();

        $this->addFlash('success', "La catégorie  <strong class='text-white'>$productTitle</strong> a été supprimée");

        return $this->redirectToRoute('app_admin_product');
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
    public function adminCategoryRemove($id, EntityManagerInterface $entityManager, CategoryRepository $repoCategory)
    {

        $category = $repoCategory->find($id);
        // dump($category);

        $categoryTitle = $category->getTitle();


        $entityManager->remove($category);
        $entityManager->flush();

        $this->addFlash('success', "La catégorie  <strong class='text-white'>$categoryTitle</strong> a été supprimée");

        return $this->redirectToRoute('app_admin_category');
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

<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class AppController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger, ProductRepository $repoProduct): Response
    {

        // exo : selectionner tous les produits enregistés en BDD (repository), transmettre au template les produits selectionnés (render), réaliser les traitements permettant d'afficher les produits dans le template index.twig, créer une nouvelle methode appProductDetail avec la route 'app/product/detail/{id}' / app_product_details, nouveau template 'app/product.details.html.twig', selectionner en BDD le produit, afficher les info du produits

        $dbProduct = $repoProduct->findAll();
        dump($dbProduct);
        // Sélection des 3 derniers produits
        $dbProduct = $repoProduct->getMaxProducts();


        // $dbProductFilter = $repoProduct->findBy([], ['id' => 'DESC'], 3);
        return $this->render('app/index.html.twig', [
            'dbProduct' => $dbProduct,
            // 'dbProductFilter' => $dbProductFilter,

        ]);
    }



    #[Route('/products', name: 'app_products')]
    public function appProducts(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger, ProductRepository $repoProduct): Response
    {
        $dbProduct = $repoProduct->findAll();
        dump($dbProduct);

        // Sélection des 3 derniers produits
        $dbProductFilter = $repoProduct->findBy([], ['id' => 'DESC'], 3);
        return $this->render('app/products.html.twig', [
            'dbProduct' => $dbProduct,


        ]);
    }


    #[Route('/product/detail/{id}', name: 'app_product_details')]
    public function appProductDetails($id, Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger, ProductRepository $repoProductDetails): Response
    {
        dump($repoProductDetails);
        dump($id);

        //recupération de tous les produits
        $dbProduct = $repoProductDetails->findAll();
        dump($dbProduct);

        // Recupération du produit selectionné
        // $product = $repoProductDetails->findAll($id);
        // dump($product);


        return $this->render('app/product.details.html.twig', [
            'dbProduct' => $dbProduct,
            'id' => $id,

        ]);
    }


    #[Route('/about', name: 'app_about')]
    public function appAbout(): Response
    {
        return $this->render('app/about.html.twig', []);
    }

    #[Route('/whyus', name: 'app_whyus')]
    public function appWhyus(): Response
    {
        return $this->render('app/whyus.html.twig', []);
    }


    #[Route('/testimonial', name: 'app_testimonial')]
    public function appTestimonial(): Response
    {
        return $this->render('app/testimonial.html.twig', []);
    }

    #[Route('/myccount', name: 'app_my_account')]
    public function appAccount(): Response
    {
        return $this->render('app/account.html.twig', []);
    }
}

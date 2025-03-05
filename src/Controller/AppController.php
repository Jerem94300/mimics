<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class AppController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(): Response
    {
        return $this->render('app/index.html.twig', [
        ]);
    }

    #[Route('/products', name: 'app_products')]
    public function appProducts(): Response
    {
        return $this->render('app/products.html.twig', []);
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


}

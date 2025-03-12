<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

final class CartController extends AbstractController
{
    #[Route('/cart', name: 'app_cart')]
    public function cart(SessionInterface $session, ProductRepository $repoProduct): Response
    {
        //on recupere le panier dans la session
        $cart = $session->get('cart');
        dump($cart);
        // on initialise les données
        $dataCart = [];
        $total = 0;
        //on boucle la session
        // $id stock pour chaque tour de boucle l'id d'un produit
        // $quantity receptionne pour chaque tour la quantité
        if (!empty($cart)) {


            foreach ($cart as $id => $quantity) {
                //On selectionne en bdd les infos des produits
                $product = $repoProduct->find($id);
                dump($product);
                // on ajoute dans le tableau Array les données
                $dataCart[] = [
                    "product" => $product, // on envoi l'objet Entity product dans l'array
                    "quantity" => $quantity
                ];

                $total += $product->getPrice() * $quantity;
            }
        }

        dump($dataCart);
        dump($total);
        return $this->render('cart/index.html.twig', [
            'dataCart' => $dataCart,
            'total' => $total

        ]);
    }

    #[Route('/cart/add/{id}', name: 'app_cart_add')]
    public function cartAdd(Request $request, Product $product, SessionInterface $session)
    {
        dump($request);
        dump($product);

        //Création du panier de session
        $cart = $session->get("cart", []);

        $id = $product->getId();
        //on stock la quantite saisie dans une variable
        $quantity = $request->request->get("quantity");

        dump($cart);
        dump($id);
        dump($quantity);


        if (isset($cart[$id])) {
            dump('if produit existant dans le panier');
            $cart[$id] = $cart[$id] + $quantity;
        } else {
            dump('else produit est inexistant dans le panier');

            $cart[$id] = $quantity;
        }

        //on sauvergarde la session
        $session->set("cart", $cart);

        dump($cart);

        return $this->redirectToRoute(('app_cart'));
    }
}

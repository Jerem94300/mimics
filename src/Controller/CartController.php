<?php

namespace App\Controller;

use App\Entity\OrderDetails;
use App\Entity\Orders;
use App\Entity\Product;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class CartController extends AbstractController
{
    #[Route('/cart', name: 'app_cart')]
    public function cart(SessionInterface $session, ProductRepository $repoProduct): Response
    {
        //on recupere le panier dans la session
        $cart = $session->get('cart');
        // dump($cart);
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
                // dump($product);
                // on ajoute dans le tableau Array les données
                $dataCart[] = [
                    "product" => $product, // on envoi l'objet Entity product dans l'array
                    "quantity" => $quantity
                ];

                $total += $product->getPrice() * $quantity;
            }
        }

        // dump($dataCart);
        // dump($total);
        return $this->render('cart/index.html.twig', [
            'dataCart' => $dataCart,
            'total' => $total

        ]);
    }

    #[Route('/cart/add/{id}', name: 'app_cart_add')]
    public function cartAdd(Request $request, Product $product, SessionInterface $session)
    {
        // dump($request);
        // dump($product);

        //Création du panier de session
        $cart = $session->get("cart", []);

        $id = $product->getId();
        //on stock la quantite saisie dans une variable
        $quantity = $request->request->get("quantity");

        // dump($cart);
        // dump($id);
        // dump($quantity);


        if (isset($cart[$id])) {
            dump('if produit existant dans le panier');
            $cart[$id] = $cart[$id] + $quantity;
        } else {
            dump('else produit est inexistant dans le panier');

            $cart[$id] = $quantity;
        }

        //on sauvergarde la session
        $session->set("cart", $cart);

        // dump($cart);

        return $this->redirectToRoute(('app_cart'));
    }

    #[Route('/cart/remove/{id}', name: 'app_cart_remove')]
    public function adminCartRemove($id, SessionInterface $session, EntityManagerInterface $entityManager, ProductRepository $repoProduct)
    {
        // Récupérer le panier depuis la session
        $cart = $session->get('cart', []);

        // Vérifier si le produit existe dans le panier
        if (isset($cart[$id])) {
            unset($cart[$id]); // Supprimer le produit du panier
            $session->set('cart', $cart); // Mettre à jour la session
        }

        $this->addFlash('success', "Le produit a été supprimé du panier");

        return $this->redirectToRoute('app_cart');
    }

    #[Route('/cart/delete', name: 'app_cart_delete')]
    public function cardDeleteAll(SessionInterface $session)
    {
        $session->remove('cart');

        $this->addFlash('success', "Le Panier a été vidé");

        return $this->redirectToRoute('app_cart');
    }


    //controle du stock
    #[Route('/cart/payment', name: 'app_cart_payment')]
    public function cartPayment(SessionInterface $session, ProductRepository $productRepository, EntityManagerInterface $entityManager)
    {
        $cart = $session->get('cart');
        $total = 0;
        dump($cart);

        foreach ($cart as $id => $quantity) {

            $product = $productRepository->find($id);
            $stockDb = $product->getStock();
            dump($stockDb);
            //si lm stock en BDD est supérieure à la quantité demandée
            if ($stockDb < $quantity) {
                //si le stock est superieur à 0 mais inferieur à la quantité demandée
                if ($stockDb > 0) {
                    //   on entre dansd la condition si le stock est insuffisant par rapport à la quantité demandée
                    dump("article " . $product->getTitle() . "  stock insuffisant.");
                    dump("Stock restant : " . $stockDb);
                    dump("Quantitée commandée : " . $stockDb);

                    $this->addFlash('warning', "La quantité de l'article <strong>" . $product->getTitle() . "</strong> a été réduite car le stock est insuffisant.");


                    $cart[$id] = $stockDb;
                } else {
                    //Sinon le stock est à 0, alors on supprime le produit de la session
                    dump("article " . $product->getTitle() . "  rupture de stock");
                    dump("Stock restant : " . $stockDb);
                    dump("Quantitée commandée : " . $stockDb);
                    $this->addFlash('danger', "L'article <strong>" . $product->getTitle() . "</strong> a été retiré du panier car il est en rupture de stock");

                    unset($cart[$id]); // Supprimer le produit du panier



                }
                $error = true;
                $session->set('cart', $cart); // Mettre à jour la session

                # code...
            }
            $total += $product->getPrice() * $quantity;
        }
        // Requete INSERT
        if (!isset($error)) {
            // Insertion dans la table SQL orders
            $order = new Orders;
            $order->setUser($this->getUser());

            $orderNumber = "MIMICS-" . date('dmY') . '-' . uniqid();
            $order->setOrderNumber($orderNumber);
            $order->setRising($total);
            $order->setCreatedAT(new \DateTimeImmutable());
            $order->setState('En cours de traitement');

            $entityManager->persist($order);
            $entityManager->flush();

            // Insertion dans la table order_detail



            foreach ($cart as $id => $quantity) {
                $orderDetails = new OrderDetails;
                $product = $productRepository->find($id);
                $orderDetails->setOrders($order);
                $orderDetails->setProduct($product);
                $orderDetails->setQuantity($quantity);
                $orderDetails->setPrice($product->getPrice());

                //on met à jour le stock
                $product->setStock($product->getStock() - $quantity);





                $entityManager->persist($product);
                $entityManager->persist($orderDetails);
                $entityManager->flush();
            }

            $this->addFlash('success', "Le paiement a été effectué.  Numero de commande : <strong>$orderNumber</strong>");

            $session->remove('cart');
        }



        return $this->redirectToRoute('app_cart');
    }
}

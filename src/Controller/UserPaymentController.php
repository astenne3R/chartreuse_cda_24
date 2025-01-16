<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\User;

final class UserPaymentController extends AbstractController{
    #[Route('/user/payment', name: 'app_user_payment')]
    public function index(): Response
    {
        return $this->render('user_payment/index.html.twig', [
            'controller_name' => 'UserPaymentController',
        ]);
    }

    // #[Route('/user/new_payment', name: 'app_new_user_payment')]
    // public function payment(): Response
    // {
    //     $user = $this->getUser();
    //     if (!$user) {
    //         $this->addFlash('danger', 'Vous devez être connecté pour ajouter des produits au panier');

    //         return $this->redirectToRoute('app_login');
    //     }

    //     // Récupérer ou créer le panier
    //     $cart = $cartRepository->findOneBy(['user' => $user]);
    //     if (!$cart) {
    //         $this->addFlash('danger', 'Vous n\'avez rien dasn votre panier ');
    //         return $this->redirectToRoute('app_home');

    //     }
        
    //     // Ajouter le produit au panier
    //     if (!$cart->getProducts()->contains($product)) {
    //         $cart->addProduct($product);
    //         $cart->setQuantity($cart->getQuantity() + 1);

    //         // Mettre à jour le stock
    //         $product->setStock($product->getStock() - 1);

    //         $entityManager->persist($cart);
    //         $entityManager->flush();

    //         $this->addFlash('success', 'Produit ajouté au panier');
    //     } else {
    //         $this->addFlash('warning', 'Ce produit est déjà dans votre panier');
    //     }

    //     return $this->redirectToRoute('app_home');
    // }
}

<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use App\Entity\Cart;
use App\Entity\Product;
use App\Repository\CartRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class UserCartController extends AbstractController{
    #[Route('/user/cart', name: 'app_user_cart')]
    public function index(CartRepository $cartRepository): Response
    {
        $user = $this->getUser();
        if(!$user) {
            return $this->redirectToRoute('app_login');
        }

        $cart = $cartRepository->findOneBy(['user' => $user]);
        return $this->render('user_cart/index.html.twig', [
            'cart' => $cart,
        ]);
    }

    #[Route('/add-to-cart/{id}', name: 'app_user_cart_add', methods: ['POST'])]
    public function addToCart(
        Request $request,
        Product $product,
        EntityManagerInterface $entityManager,
        CartRepository $cartRepository,
    ): Response {
        $user = $this->getUser();
        if (!$user) {
            $this->addFlash('danger', 'Vous devez être connecté pour ajouter des produits au panier');

            return $this->redirectToRoute('app_login');
        }

        // Vérifier le stock
        if ($product->getStock() <= 0) {
            $this->addFlash('danger', 'Produit en rupture de stock');

            return $this->redirectToRoute('app_home');
        }

        // Récupérer ou créer le panier
        $cart = $cartRepository->findOneBy(['user' => $user]);
        if (!$cart) {
            $cart = new Cart();
            $cart->setUser($user);
            $cart->setQuantity(0);
        }

        // Ajouter le produit au panier
        if (!$cart->getProducts()->contains($product)) {
            $cart->addProduct($product);
            $cart->setQuantity($cart->getQuantity() + 1);

            // Mettre à jour le stock
            $product->setStock($product->getStock() - 1);

            $entityManager->persist($cart);
            $entityManager->flush();

            $this->addFlash('success', 'Produit ajouté au panier');
        } else {
            $this->addFlash('warning', 'Ce produit est déjà dans votre panier');
        }

        return $this->redirectToRoute('app_home');
    }
}

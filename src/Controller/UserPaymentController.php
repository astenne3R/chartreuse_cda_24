<?php

namespace App\Controller;

use Symfony\Compsonent\HttpFoundation\RedirectResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use App\Repository\CartRepository;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Stripe\Stripe;
use Stripe\Checkout\Session;

final class UserPaymentController extends AbstractController{
    #[Route('/payment-user', name: 'app_user_payment')]
    public function index(): Response
    {
        return $this->render('user_payment/index.html.twig', [
            'controller_name' => 'UserPaymentController',
        ]);
    }

    #[Route('/checkout', name: 'app_payment_user_checkout', methods: ['POST'])]
    public function checkout(Request $request, CartRepository $cartRepository, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        if (!$user) {
            $this->addFlash('danger', 'Vous devez être connecté pour ajouter des produits au panier');
            return $this->redirectToRoute('app_login');
        }

        $fullName = $request->request->get('fullName');
        $address = $request->request->get('address');
        $zipCode = $request->request->get('zipCode');
        $city = $request->request->get('city');
        $phone = $request->request->get('phone');

        // return $this->redirectToRoute('app_home');
                // Récupérer ou créer le panier
        $cart = $cartRepository->findOneBy(['user' => $user]);
        if (!$cart) {
            $this->addFlash('danger', 'Vous n\'avez rien dasn votre panier ');
            return $this->redirectToRoute('app_home');
        }

        Stripe::setApiKey($_ENV['STRIPE_SECRET_KEY']);

        $lineItems = [];
        foreach ($cart->getProducts() as $product) {
            $lineItems[] = [
                'price_data' => [
                    'currency' => 'eur',
                    'product_data' => [
                        'name' => $product->getName(),
                        'description' => $product->getDescription(),
                    ],
                    'unit_amount' => (int) ($product->getHtPrice() * (1 + $product->getVatRate() / 100) * 100),
                ],
                'quantity' => 1,
            ];
        }
        // dd($lineItems);

        $checkoutSession = Session::create([
            'payment_method_types' => ['card'],
            'line_items' => $lineItems,
            'mode' => 'payment',
            'success_url' => $this->generateURL('app_home', [], UrlGeneratorInterface::ABSOLUTE_URL),
            'cancel_url' => $this->generateURL('app_home', [], UrlGeneratorInterface::ABSOLUTE_URL),
            'customer_email'=> $user->getEmail(),
            'metadata' => [
                'address_id' => 1,
            ],
        ]);

        // $entityManager->flush();

        return $this->redirect($checkoutSession->url);
        // use Symfony\Compsonent\HttpFoundation\RedirectResponse;

        

        // Ajouter le produit au panier
        // if (!$cart->getProducts()->contains($product)) {
        //     $cart->addProduct($product);
        //     $cart->setQuantity($cart->getQuantity() + 1);

        //     // Mettre à jour le stock
        //     $product->setStock($product->getStock() - 1);

        //     $entityManager->persist($cart);
        //     $entityManager->flush();

        //     $this->addFlash('success', 'Produit ajouté au panier');
        // } else {
        //     $this->addFlash('warning', 'Ce produit est déjà dans votre panier');
        // }
    }
}
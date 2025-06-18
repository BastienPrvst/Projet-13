<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ProductController extends AbstractController
{

    public function __construct(
        private readonly ProductRepository $productRepository
    )
    {
    }

    #[Route('/', name: 'app_home')]
    public function index(): Response
    {

        $allProducts = $this->productRepository->findAll();

        return $this->render('home.html.twig',
        [
            'products' => $allProducts,
        ]);
    }

    #[Route('/produit/{id}', name: 'app_product')]
    public function showProduct(Product $product, Request $request): Response
    {
        return $this->render('product.html.twig', [
            'product' => $product,
            'success' => $request->query->get('success'),
        ]);
    }

    #[Route('/panier/{id}', name: 'app_basket_add', requirements: ['id'=>'\d+'])]
    public function basket(Product $product, Request $request): Response
    {
        $session = $request->getSession();
        $basket = $session->get('basket', []);
        $productId = $product->getId();

        if (!isset($basket[$productId])) {
            $basket[$productId] = 1;
        } else {
            $basket[$productId]++;
        }
        $session->set('basket', $basket);

        $success = 'Le produit à bien été ajouté au panier';

        return $this->redirectToRoute('app_product', [
            'id' => $productId,
            'success' => $success,
        ]);

    }

    #[Route(path: '/panier', name: 'app_basket_show')]
    public function showBasket(Request $request, ProductRepository $productRepository): Response
    {
        $session = $request->getSession();
        $allProducts = $session->get('basket');
        $realBasket = [];
        if (!empty($allProducts)) {
            foreach ($allProducts as $productId => $quantity) {
                $product = $productRepository->find($productId);
                $realBasket [$productId]['product'] = $product;
                $realBasket [$productId]['quantity'] = $quantity;
            }

        }

        return $this->render('basket.html.twig', [
            'products' => $realBasket,
        ]);
    }

    #[Route(path: '/supprimer-panier', name: 'app_basket_delete')]
    public function deleteBasket(Product $product, Request $request): Response
    {
        $session = $request->getSession();
        $session->remove('basket');
        return $this->redirectToRoute('app_basket_show');
    }

}

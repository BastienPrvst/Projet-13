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
        $session = $request->getSession();
        $basket = $session->get('basket');
        $basketQuantity = $basket[$product->getId()] ?? 0;

        return $this->render('product.html.twig', [
            'product' => $product,
            'success' => $request->query->get('success'),
            'basketQuantity' => $basketQuantity,
        ]);
    }

    #[Route('/ajouter-panier/{id}', name: 'app_basket_add', requirements: ['id'=>'\d+'])]
    public function basket(Product $product, Request $request): Response
    {
        $session = $request->getSession();
        $basket = $session->get('basket', []);
        $productId = $product->getId();
        (int)$quantity = $request->get('quantity');

        if ((int)$quantity === 0 && !isset($basket[$productId])) {
            return $this->redirectToRoute('app_product',
                ['id' => $productId]);
        }


        if (!isset($basket[$productId])) {
            $success = sprintf("Le produit %s à bien été ajouté au panier x %d.", $product->getName(), $quantity);
        } else {
            $success = sprintf("Le produit %s à bien été mis à jour.", $product->getName());
        }
        $basket[$productId] = (int)$quantity;

        if ($basket[$productId] === 0) {
            unset($basket[$productId]);
        }

        $session->set('basket', $basket);

        return $this->redirectToRoute('app_product', [
            'id' => $productId,
            'success' => $success,
        ]);

    }

    #[Route(path: '/voir-panier', name: 'app_basket_show')]
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
    public function deleteBasket(Request $request): Response
    {
        $session = $request->getSession();
        $session->remove('basket');
        return $this->redirectToRoute('app_basket_show');
    }

}

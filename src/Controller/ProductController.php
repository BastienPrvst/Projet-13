<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

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
    public function showProduct(Product $product): Response
    {
        return $this->render('product.html.twig', [
            'product' => $product,
        ]);
    }

    #[Route('/basket/{id}/{quantity}', name: 'app_basket', requirements: ['id'=>'\d+', 'quantity'=>'\d+'])]
    public function basket(): Response
    {
        return $this->render('basket.html.twig');
    }

}

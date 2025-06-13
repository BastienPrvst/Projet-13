<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ProductController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(): Response
    {
        return $this->render('home.html.twig');
    }

    #[Route('/produit/{id}', name: 'app_product')]
    public function showProduct($id): Response
    {
        return $this->render('product.html.twig', []);
    }
}

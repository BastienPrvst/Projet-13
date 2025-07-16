<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\ProductRepository;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\SerializerInterface;

class ApiController
{
    #[Route(path: '/api/products', name: 'app_api_products', methods: ['GET'])]
    public function apiProducts(ProductRepository $productRepository, SerializerInterface $serializer): JsonResponse
    {
        try {
            $products = $productRepository->findAll();
            $serializedProducts = $serializer->serialize($products, 'json');

            return new JsonResponse([
                'products' => $serializedProducts
            ], Response::HTTP_OK, [], true);

        }catch (ExceptionInterface $e) {
            return new JsonResponse([
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR, [], false);
        }


    }


}

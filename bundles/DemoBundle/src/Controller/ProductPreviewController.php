<?php

namespace DemoBundle\Controller;

use Pimcore\Model\DataObject\Product;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProductPreviewController extends AbstractController
{
    /**
     * @Route("/preview/product/{id}", name="product_preview")
     */
    public function previewProductAction(int $id): Response
    {
        $product = Product::getById($id);

        if (!$product) {
            return new JsonResponse(['error' => 'Product not found'], 404);
        }

        // Render a Twig template to HTML
        return $this->render('@DemoBundle/ProductPreview/preview.html.twig', [
            'product' => $product,
        ]);

        // Create a JSON response with HTML content
//        return new JsonResponse(['html' => $htmlContent,'id'=>$id]);
    }


}

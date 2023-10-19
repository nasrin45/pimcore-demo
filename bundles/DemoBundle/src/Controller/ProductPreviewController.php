<?php

namespace DemoBundle\Controller;

use Pimcore\Model\DataObject\Product;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProductPreviewController extends AbstractController
{
    /**
     * @Route("/preview/product", name="product_preview")
     */
    public function previewProductAction(Request $request): Response
    {
        $product = Product::getById(6);

        // Check if the product exists
        if (!$product) {
            throw $this->createNotFoundException('Product not found');
        }

        return $this->render('@DemoBundle/ProductPreview/preview.html.twig', [
            'product' => $product,
        ]);
    }
}

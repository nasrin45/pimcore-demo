<?php

namespace App\Controller;

use Pimcore\Controller\FrontendController;
use Symfony\Bridge\Twig\Attribute\Template;
use Symfony\Component\HttpFoundation\Request;

class ContentController extends FrontendController
{
    #[Template('content/default.html.twig')]
    public function defaultAction(Request $request): array
    {
        return [];
    }
    public function productAction(Request $request): \Symfony\Component\HttpFoundation\Response
    {
        return $this->render('content/product.html.twig');
    }
}

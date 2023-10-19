<?php

namespace DemoBundle\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
    /**
     * @Route("/demo")
     */
    public function indexAction(Request $request): Response
    {
        return new Response('Hello world from Demo');
    }

}

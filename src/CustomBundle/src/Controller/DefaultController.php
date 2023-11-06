<?php

namespace CustomBundle\Controller;

use App\Model\DataObject\Faculty;
use Exception;
use Pimcore\Controller\FrontendController;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
    /**
     * @Route("/custom")
     */
    public function indexAction(Request $request): Response
    {
        return new Response('Hello world from custom');
    }

    /**
     * @throws Exception
     * @Route("/create-faculty/{name}", name="custom_bundle_faculty")
     */
    public function createFacultyAction(Request $request, string $name): Response
    {
        $faculty = new Faculty();
        $faculty->setName($name);

        return $this->render('@CustomBundle/Default/faculty.html.twig', [
            'faculty' => $faculty,
        ]);
    }
}

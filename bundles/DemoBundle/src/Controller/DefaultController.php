<?php

namespace DemoBundle\Controller;


use DemoBundle\Model\Vote\Listing;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
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

    /**
     * @Route("/dao", name="dao")
     */
    public function daoAction(): JsonResponse
    {
        $vote = new \DemoBundle\Model\Vote();
        $vote->setScore(mt_rand(1, 999));
        $vote->setUsername('foobar!'.mt_rand(1, 999));
        $vote->save();
//        dd($vote);

        $list = new Listing();
        $list->setCondition("score > ?", array(1));
        $votes = $list->load();
//        dd($list);


        return new JsonResponse(['message' => 'Vote Added Successfully']);
    }

}

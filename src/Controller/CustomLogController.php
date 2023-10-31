<?php

namespace App\Controller;

use App\Service\CustomService;
use DemoBundle\Model\Vote\Listing;
use Pimcore\Controller\FrontendController;
use Pimcore\Tool\DeviceDetector;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;


class CustomLogController extends FrontendController
{
    /**
     * @Route("/customlog", name="customlog")
     */
    public function someAction(CustomService $customLogger): JsonResponse
    {
        $device = DeviceDetector::getInstance();
        $device->getDevice();
        echo $device;

        try {
            $customLogger->logCustomMessage();

            return new JsonResponse(['message' => 'Logger successful']);
        } catch (\Exception $e) {
            // Handle the exception, log the error, or return an error response
            return new JsonResponse(['message' => 'Logger failed'], 500); // Return an error response
        }

    }
}

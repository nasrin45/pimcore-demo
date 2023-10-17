<?php

namespace App\Controller;

use App\Model\DataObject\Home;
use App\Service\CustomService;
use Pimcore\Controller\FrontendController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Pimcore\Tool\DeviceDetector;
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

<?php

namespace TrackingBundle\Controller;

use DateTime;
use Exception;
use Pimcore\Db;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use TrackingBundle\Model\AdminActivity;
use TrackingBundle\Model\AdminActivity\Dao;
use TrackingBundle\Model\AdminActivity\Listing;
use Symfony\Component\Routing\Annotation\Route;


class TrackingController extends AbstractController
{

    private $dao;

    public function __construct(Dao $dao)
    {
        $this->dao = $dao;
    }

    /**
     * @throws Exception
     */
    public function listAction(): Response
    {
        $activity = $this->dao->getAdminActivities();


        return $this->render('@TrackingBundle/Tracking/listing.html.twig', [
            'activity' => $activity,
        ]);
    }

    /**
     * @Route("/track", name="track", methods={"GET"})
     */
    public function trackAction(Request $request): JsonResponse
    {
        // Instantiate the AdminActivity listing
        $listing = new Listing();


        // Fetch data from the database
        $data = $listing->load();
        $totalRecords = count($data);

        // Transform the data as needed (e.g., to an array)
        $formattedData = [];
        foreach ($data as $item) {
            $formattedData[] = [
                'id' => $item->getId(),
                'admin_id' => $item->getAdminId(),
                'action' => $item->getAction(),
                'date' => $item->getTimestamp(),
            ];
        }

        $page = $request->query->get('page', 1);
        $pageSize = 50;
        $offset = ($page - 1) * $pageSize;
        $pagedData = array_slice($formattedData, $offset, $pageSize);
        return new JsonResponse([
            'total' => $totalRecords,
            'list' => $formattedData,
        ]);
    }
}

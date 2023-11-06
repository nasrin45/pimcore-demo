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
use Symfony\Component\Yaml\Yaml;
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
        $page = $request->query->get('page', 1);
        $pageSize = 500;
        $offset = ($page - 1) * $pageSize;
        $pagedData = array_slice($data, $offset, $pageSize);

        // Transform the data as needed (e.g., to an array)
        $formattedData = [];
        foreach ($pagedData as $item) {
            $formattedData[] = [
                'id' => $item->getId(),
                'admin_id' => $item->getAdminId(),
                'action' => $item->getAction(),
                'date' => $item->getTimestamp(),
            ];
        }
        return new JsonResponse([
            'total' => $totalRecords,
            'list' => $formattedData,
        ]);
    }

    /**
     * @Route("/customButton", name="customButton")
     */
        public function saveDataAction(Request $request): Response
        {
            $formData =  json_decode($request->get('formData'), true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                return new JsonResponse(['success' => false, 'message' => 'Invalid JSON data']);
            }

            // Continue with your logic to save the data to the YAML file
            $configPath = $this->getParameter('kernel.project_dir') . '/bundles/TrackingBundle/config/custom_settings/custom_settings.yaml';

            $yamlData = Yaml::dump($formData, 4);

            // Save data to the YAML file
            if (file_put_contents($configPath, $yamlData) === false) {
                return new JsonResponse(['success' => false, 'message' => 'Failed to save data']);
            }

            return new JsonResponse(['success' => true, 'message' => 'Data saved successfully']);
        }

    /**
     * @Route("/retrieveData", name="retrieveData")
     */
    public function retrieveDataAction(): Response
    {

        $configPath = $this->getParameter('kernel.project_dir') . '/bundles/TrackingBundle/config/custom_settings/custom_settings.yaml';
        if (file_exists($configPath)) {
            $yamlData = file_get_contents($configPath);
            $data = Yaml::parse($yamlData);
            $showData = isset($data['permission']) && $data['permission'] === true;

            if ($showData) {
                // Pass the parsed data to your Twig template
                return $this->render('@TrackingBundle/yaml.html.twig', ['data' => $data]);
            } else {
                return new JsonResponse(['error' => 'Permission not given']);
            }

        } else {
            return new JsonResponse(['error' => 'YAML file not found']);
        }
    }
}

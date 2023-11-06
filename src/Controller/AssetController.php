<?php

namespace App\Controller;

use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Pimcore\Model\Asset;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class AssetController extends AbstractController
{
    /**
     * @Route("/create-asset", name="create_asset")
     * @throws Exception
     */
    public function createAssetAction(): JsonResponse
    {
        $assetPath = '/images/';
        $assetFilename = 'my_asset.png';

        $asset = new Asset();
        $asset->setParentId(1);
        $asset->setFilename($assetFilename);
        $asset->setParent(Asset::getByPath($assetPath));
        $asset->setData(file_get_contents($_SERVER['DOCUMENT_ROOT'] . '/images/logo.png'));

        $asset->save();

        return new JsonResponse(['message' => 'Asset created successfully']);
    }

    /**
     * @throws Exception
     */
    public function updateAssetAction(): JsonResponse
    {
        $newAsset = Asset::getById(35);
        if ($newAsset instanceof Asset) {

            $newAsset->setFilename('mylogo.png');

            $newAsset->save();
            return new JsonResponse(['message' => 'Asset name updated successfully']);
        } else {
            return new JsonResponse(['message' => 'Asset not found or an error occurred'], 404);
        }
    }
}


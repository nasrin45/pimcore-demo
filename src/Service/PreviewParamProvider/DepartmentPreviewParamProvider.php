<?php

namespace App\Service\PreviewParamProvider;

use App\Website\LinkGenerator\DepartmentLinkGenerator;
use Pimcore\Model\DataObject\ClassDefinition\PreviewGeneratorInterface;
use Pimcore\Model\DataObject\Concrete;

class DepartmentPreviewParamProvider implements PreviewGeneratorInterface
{
    protected DepartmentLinkGenerator $departmentLinkGenerator;

    public function __construct(DepartmentLinkGenerator $departmentLinkGenerator)
    {
        $this->departmentLinkGenerator = $departmentLinkGenerator;
    }

    public function generatePreviewUrl(Concrete $object, array $params): string
    {
        $additionalParams = [];
        foreach($this->getPreviewConfig($object) as $paramStore) {
            $paramName = $paramStore['name'];
            if($paramValue = $params[$paramName]) {
                $additionalParams[$paramName] = $paramValue;
            }
        }

        return $this->departmentLinkGenerator->generate($object, $additionalParams);
    }

    public function getPreviewConfig(Concrete $object): array
    {
        return [
            [
                'name' => '_locale',
                'label' => 'Locale',
                'values' => [
                    'English' => 'en',
                    'German' => 'de'
                ],
                'defaultValue' => 'en'
            ],
            [
                'name' => 'otherParam',
                'label' => 'Other',
                'values' => [
                    'Label Text' => 'value',
                    'Option #2' => 2,
                    'Custom Option' => 'custom'
                ],
                'defaultValue' => 'value'
            ]
        ];
    }
}

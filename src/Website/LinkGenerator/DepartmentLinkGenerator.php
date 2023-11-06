<?php

namespace App\Website\LinkGenerator;

use Pimcore\Model\DataObject;
use Pimcore\Model\DataObject\ClassDefinition\LinkGeneratorInterface;
use Pimcore\Model\DataObject\Department;

class DepartmentLinkGenerator implements LinkGeneratorInterface
{
    private DepartmentLinkGenerator $departmentLinkGenerator;

    public function __contruct(DepartmentLinkGenerator $departmentLinkGenerator)
    {
        $this->departmentLinkGenerator = $departmentLinkGenerator;
    }

    public function generate(object $object, array $params = []): string
    {
        if (!($object instanceof \Pimcore\Model\DataObject\Department)) {
            throw new \InvalidArgumentException('Given object is not a Department');
        }

        return $this->doGenerate($object, $params);
    }

    protected function doGenerate(\Pimcore\Model\DataObject\Department $object, array $params): string
    {
//        $departmentName = $this->getDeptName($object);

        // You can use the department name in your URL generation logic
        $departmentId = $object->getClassName();

        // Generate the URL based on your requirements
        $url = '/department' . '/' . $departmentId;

        return $url;
    }

//    protected function getDeptName(\Pimcore\Model\DataObject\Department $department): ?string
//    {
//        // Get the classification store object
//        $classificationStore = $department->getDept();
//
//        // Define the group name to look for
//        $groupNameToFind = "department-info";
//
//        // Iterate through the groups in the classification store
//        foreach ($classificationStore->getGroups() as $group) {
//            $groupName = $group->getConfiguration()->getName();
//
//            // Check if the group name matches the one we're looking for
//            if ($groupName === $groupNameToFind) {
//                foreach ($group->getKeys() as $key) {
//                    $keyConfiguration = $key->getConfiguration();
//
//                    // Check if the key name is "department"
//                    if ($keyConfiguration->getName() === "department") {
//                        // Retrieve the department name from the key
//                        $value = $key->getValue();
//                        if ($value instanceof \Pimcore\Model\DataObject\Data\QuantityValue) {
//                            $value = (string)$value;
//                        }
//
//                        return $value;
//                    }
//                }
//            }
//        }
//
//        return null; // Return null if not found or classification store is not set
//    }
}

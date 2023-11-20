<?php

namespace App\PathFormatter;


use Pimcore\Model\DataObject\ClassDefinition\PathFormatterInterface;
use Pimcore\Model\DataObject\Concrete;
use Pimcore\Model\DataObject\Department;
use Pimcore\Model\Element\ElementInterface;


class DepartmentPathFormatter implements PathFormatterInterface
{
    public function formatPath(array $result, ElementInterface $source, array $targets, array $params): array
    {
        foreach ($targets as $key => $item) {
            if ($item["type"] == "object") {
                $targetObject = Concrete::getById($item["id"]);
                if ($targetObject instanceof Department) {
                    $classificationStore = $targetObject->getDept();
                    $groupNameToFind = "department-info";

                    foreach ($classificationStore->getGroups() as $group) {
                        $groupName = $group->getConfiguration()->getName();

                        if ($groupName === $groupNameToFind) {
                            foreach ($group->getKeys() as $key) {
                                $keyConfiguration = $key->getConfiguration();

                                if ($keyConfiguration->getName() === "department") {
                                    $departmentName = $key->getValue();
                                    $result[$key] = $departmentName;
                                    break;
                                }
                            }
                        }
                    }
                }
            }
        }

        return $result;
    }
}

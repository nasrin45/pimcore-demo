<?php

namespace TrackingBundle\Command;

use Carbon\Carbon;
use League\Csv\Exception;
use League\Csv\UnavailableStream;
use Pimcore\Model\DataObject;
use Pimcore\Model\DataObject\Data\GeoCoordinates;
use Pimcore\Model\DataObject\Fieldcollection;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use League\Csv\Reader;
use Pimcore\Model\DataObject\General;

class CreateDataObjectsCommand extends Command
{
    protected function configure(): void
    {
        $this->setName('tracking:create-data-object')
            ->setDescription('Create a data object based on a data object class and attributes from a CSV file')
            ->addArgument('csvFile', InputArgument::REQUIRED, 'Path to the CSV file');
    }

    /**
     * @throws UnavailableStream
     * @throws Exception
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $csvFile = $input->getArgument('csvFile');

        if (!file_exists($csvFile)) {
            $output->writeln('CSV file does not exist.');
            return Command::FAILURE;
        }

        $csv = Reader::createFromPath($csvFile);
        $csv->setHeaderOffset(0);

        $parentObject = DataObject::getById(25);

        if (!$parentObject) {
            $output->writeln('Parent object not found.');
            return Command::FAILURE;
        }


        foreach ($csv->getRecords() as $record) {
            $key = $record['key'];
            $dataObject = General::getByPath('/Products/' . $key);

            $data = [];

            foreach ($record as $field => $value) {
                $data[$field] = $this->castValue($field, $value);
            }

            if ($dataObject) {
                // Update the existing object
                $dataObject->setValues($data);
            } else {
                // Create a new object
                $dataObject = General::create($data);
                $dataObject->setKey($key);
            }

            $this->handleFieldCollections($record, $dataObject);
            $this->handleBrick($record, $dataObject);
            $this->handleBlock($record, $dataObject);

            $dataObject->setParentId($parentObject->getId());
            $dataObject->save();
        }
        $output->writeln("Data objects are created or updated");
        return Command::SUCCESS;
    }

    private function castValue(string $field, mixed $value): mixed
    {
//        var_dump("Field: " . $field, "Value: " . $value);

        if ($field === 'location') {
            $coordinates = explode(',', $value);
            if (count($coordinates) === 2) {
                [$latitude, $longitude] = $coordinates;
                return new GeoCoordinates($latitude, $longitude);
            } else {
                return null;
            }
        } elseif ($field === 'dob') {
            return Carbon::parse($value);
        } elseif ($field === 'number') {
            return (float) $value;
        }elseif ($field === 'gender') {
            return $value;
        } else {
            return $value;
        }
    }


    private function handleFieldCollections(array $record, DataObject $dataObject): void
    {
        if (isset($record['collection/0/description'])) {
            $fields = $dataObject->getCollection();

            if (!$fields) {
                $fields = new Fieldcollection();
                $dataObject->setCollection($fields);
            } else {
                foreach ($fields as $existingItem) {
                    if ($existingItem->getDescription() === $record['collection/0/description']) {
                        // Update the existing entry
                        $existingItem->setDescription($record['collection/0/description']);

                        foreach ($record as $field => $value) {
                            // Exclude special fields like 'name' from updating
                            if (!in_array($field, ['collection/0/description'])) {
                                $this->setFieldcollectionValue($existingItem, $field, $value);
                            }
                        }

                        return;
                    }
                }
            }

            $entry = new Fieldcollection\Data\General();
            $entry->setDescription($record['collection/0/description']);

            foreach ($record as $field => $value) {
                // Exclude special fields like 'name' from setting
                if (!in_array($field, ['collection/0/description'])) {
                    $this->setFieldCollectionValue($entry, $field, $value);
                }
            }

            $fields->add($entry);
        }
    }

    private function setFieldCollectionValue(Fieldcollection\Data\General $entry, string $field, mixed $value): void
    {
        // Use a naming convention to identify field types and set values dynamically
        $setterMethod = 'set' . ucfirst($field);

        if (method_exists($entry, $setterMethod)) {
            $entry->$setterMethod($value);
        }
    }


    private function handleBrick(array $record, DataObject\Concrete $dataObject): void
    {
        $brickKey = 'brick';
        $brick = $dataObject->getBrick();

        if (!$brick) {
            $brick = new DataObject\Objectbrick($dataObject, $brickKey);
            $dataObject->setBrick($brick);
        }

        $brickData = $brick->get($brickKey);

        if (!$brickData) {
            $brickData = new \Pimcore\Model\DataObject\Objectbrick\Data\Brick($dataObject);
        }

        foreach ($record as $field => $value) {
            // Exclude special fields like 'TestBrick/0/district' from setting
            if (!in_array($field, ["$brickKey/0/text"])) {
                $this->setBrickValue($brickData, $field, $value);
            }
        }

        $brick->set($brickKey, $brickData);
    }

    private function setBrickValue(\Pimcore\Model\DataObject\Objectbrick\Data\Brick $brickData, string $field, mixed $value): void
    {
        // Use a naming convention to identify field types and set values dynamically
        $setterMethod = 'set' . ucfirst($field);

        if (method_exists($brickData, $setterMethod)) {
            $brickData->$setterMethod($value);
        }
    }

    /**
     * @throws \Exception
     */
    private function handleBlock(array $record, DataObject\Concrete $dataObject): void
    {
        $blockKey = 'block';
        $blockData = [];

        foreach ($record as $field => $value) {
            if (strpos($field, "$blockKey/0/") === 0) {
                // Extract the field name from the field (e.g., 'block/0/info' => 'info')
                $fieldName = substr($field, strlen("$blockKey/"));

                // Get the associated block type from the data object class configuration
                $fieldConfig = $dataObject->getClass()->getFieldDefinition($blockKey);
                $blockType = $fieldConfig->getFieldtype();

                // Create a block element for the field
                $blockElement = new \Pimcore\Model\DataObject\Data\BlockElement(
                    $fieldName,
                    $blockType,
                    $value
                );

                // Add the block element to the block data array
                $blockData[] = [$fieldName => $blockElement];
            }
        }

        $dataObject->setBlock($blockData);
        // var_dump($dataObject);
    }

}

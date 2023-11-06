<?php

namespace TrackingBundle\Command;

use Carbon\Carbon;
use League\Csv\Exception;
use League\Csv\UnavailableStream;
use Pimcore\Model\DataObject;
use Pimcore\Model\DataObject\Data\GeoCoordinates;
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

            $dataObject->setParentId($parentObject->getId());
            $dataObject->save();
        }
        $output->writeln("Data objects are created or updated");
        return Command::SUCCESS;
    }

    private function castValue(string $field, mixed $value): mixed
    {
        var_dump("Field: " . $field, "Value: " . $value);

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
        }else {
            return $value;
        }
    }
}

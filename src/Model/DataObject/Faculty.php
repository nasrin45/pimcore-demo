<?php

namespace App\Model\DataObject;

class Faculty extends \Pimcore\Model\DataObject\Concrete
{
    private string $campus;
    private string $name;

    public function setCampus(string $campus): void
    {
        $this->campus = $campus;
    }

    public function getCampus(): ?string
    {
        return $this->campus;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

}

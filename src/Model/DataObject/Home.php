<?php

namespace App\Model\DataObject;

class Home extends \Pimcore\Model\DataObject\Home
{
    private string $campus;

    public function setCampus(string $campus): void
    {
        $this->campus = $campus;
    }

    public function getCampus(): ?string
    {
        return $this->campus;
    }

}

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

    /**
     * Set location - Location
     * @param string|null $location
     * @return $this
     */
    public function setLocation(?string $location): static
    {
        // Your custom formatting logic here
        // For example, let's convert the location to uppercase
        if ($location !== null) {
            $location = strtoupper($location);
        }

        $this->markFieldDirty("location", true);

        $this->location = $location;

        return $this;
    }


}

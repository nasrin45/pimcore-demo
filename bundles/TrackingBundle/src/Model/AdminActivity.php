<?php

namespace TrackingBundle\Model;

use Pimcore\Model\AbstractModel;
use Pimcore\Model\Exception\NotFoundException;
use TrackingBundle\Model\AdminActivity\Listing;

class AdminActivity extends AbstractModel
{
    public ?int $id = null;
    private ?int $adminId = null;
    public ?string $action = null;
    public $timestamp = null;

    public static function getById(int $id): ?AdminActivity
    {
        try {
            $obj = new self;
            $obj->getDao()->getById($id);
            return $obj;
        }
        catch (NotFoundException $ex) {
            \Pimcore\Logger::warn("Activity with id $id not found");
        }

        return null;
    }


   public function getAdminId(): ?int
   {
     return $this->adminId;
   }

    public function setAdminId(?int $adminId): void
    {
        $this->adminId = $adminId;
    }

    public function setAction(?string $action): void
    {
        $this->action = $action;
    }

    public function getAction(): ?string
    {
        return $this->action;
    }

    public function setTimestamp( $timestamp): void
    {
        $this->timestamp = $timestamp;
    }

    public function getTimestamp(): mixed
    {
        return $this->timestamp;
    }

    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    public function getId(): ?int
    {
        return $this->id;
    }
}

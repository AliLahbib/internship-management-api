<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ApiResource]
class Admin extends User
{
    #[ORM\Column(length: 255)]
    private ?string $department = null;

    public function __construct()
    {
        $this->setRoles(['ROLE_ADMIN']);
    }

    public function getDepartment(): ?string
    {
        return $this->department;
    }

    public function setDepartment(string $department): self
    {
        $this->department = $department;
        return $this;
    }
} 
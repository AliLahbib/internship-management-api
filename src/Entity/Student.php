<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ApiResource]
class Student extends User
{
    #[ORM\Column(length: 255, unique: true,nullable: true)]
    private ?string $identityCardNumber = null;

    #[ORM\Column(length: 50)]
    private ?string $program = null; // Licence, Mastere, Ingénieur

    #[ORM\Column]
    private ?int $studyYear = null;

    #[ORM\OneToMany(mappedBy: 'student', targetEntity: InternshipRequest::class)]
    private Collection $internshipRequests;

    public function __construct()
    {
        $this->internshipRequests = new ArrayCollection();
        $this->setRoles(['ROLE_STUDENT']);
    }

    public function getIdentityCardNumber(): ?string
    {
        return $this->identityCardNumber;
    }

    public function setIdentityCardNumber(string $identityCardNumber): self
    {
        $this->identityCardNumber = $identityCardNumber;
        return $this;
    }

    public function getProgram(): ?string
    {
        return $this->program;
    }

    public function setProgram(string $program): self
    {
        $this->program = $program;
        return $this;
    }

    public function getStudyYear(): ?int
    {
        return $this->studyYear;
    }

    public function setStudyYear(int $studyYear): self
    {
        $this->studyYear = $studyYear;
        return $this;
    }

    public function getInternshipRequests(): Collection
    {
        return $this->internshipRequests;
    }

    public function addInternshipRequest(InternshipRequest $internshipRequest): self
    {
        if (!$this->internshipRequests->contains($internshipRequest)) {
            $this->internshipRequests[] = $internshipRequest;
            $internshipRequest->setStudent($this);
        }
        return $this;
    }

    public function removeInternshipRequest(InternshipRequest $internshipRequest): self
    {
        if ($this->internshipRequests->removeElement($internshipRequest)) {
            if ($internshipRequest->getStudent() === $this) {
                $internshipRequest->setStudent(null);
            }
        }
        return $this;
    }
}
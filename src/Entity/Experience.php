<?php

namespace App\Entity;

use App\Repository\ExperienceRepository;
use App\Validator\BanWord;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ExperienceRepository::class)]
class Experience
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::STRING, length: 255)]
    #[Assert\NotBlank()]
    #[Assert\Length(min: 2, max: 100)]
    #[BanWord()]
    private string $title = '';

    #[ORM\Column(type: Types::STRING, length: 255)]
    #[Assert\NotBlank()]
    #[Assert\Length(min: 1, max: 100)]
    #[BanWord()]
    private string $location = '';

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Assert\NotNull()]
    private ?\DateTimeInterface $startDate = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Assert\NotNull()]
    #[Assert\GreaterThan(propertyPath: "startDate", message: "End date must be greater than start date.")]
    private ?\DateTimeInterface $endDate = null;

    #[ORM\Column(type: Types::BOOLEAN)]
    #[Assert\NotNull()]
    private ?bool $isFormation = null;

    #[ORM\ManyToOne(inversedBy: 'experiences')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getLocation(): string
    {
        return $this->location;
    }

    public function setLocation(string $location): static
    {
        $this->location = $location;

        return $this;
    }

    public function getStartDate(): ?\DateTimeInterface
    {
        return $this->startDate;
    }

    public function setStartDate(\DateTimeInterface $startDate): static
    {
        $this->startDate = $startDate;

        return $this;
    }

    public function getEndDate(): ?\DateTimeInterface
    {
        return $this->endDate;
    }

    public function setEndDate(\DateTimeInterface $endDate): static
    {
        $this->endDate = $endDate;

        return $this;
    }

    public function isIsFormation(): ?bool
    {
        return $this->isFormation;
    }

    public function setIsFormation(bool $isFormation): static
    {
        $this->isFormation = $isFormation;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }
}

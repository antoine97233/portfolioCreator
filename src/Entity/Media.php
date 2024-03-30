<?php

namespace App\Entity;

use App\Repository\MediaRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: MediaRepository::class)]
#[Vich\Uploadable]
#[UniqueEntity(fields: ['user'], message: 'This user already has a media.')]
class Media
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true)]
    #[Groups('users.show')]
    private ?string $thumbnail = null;

    #[Vich\UploadableField(mapping: 'users', fileNameProperty: 'thumbnail')]
    #[Assert\Image()]
    #[Assert\NotBlank()]
    private ?File $thumbnailFile = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\OneToOne(inversedBy: 'media', cascade: ['persist', 'remove'])]
    private ?User $user = null;

    #[ORM\OneToOne(inversedBy: 'media', cascade: ['persist', 'remove'])]
    private ?Project $project = null;

    public function __construct()
    {
        $this->updatedAt = new \DateTimeImmutable();
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getThumbnail(): ?string
    {
        return $this->thumbnail;
    }

    public function setThumbnail(?string $thumbnail): static
    {
        $this->thumbnail = $thumbnail;

        return $this;
    }

    /**
     * Get the value of thumbnailFile
     */
    public function getThumbnailFile(): ?File
    {
        return $this->thumbnailFile;
    }

    /**
     * Set the value of thumbnailFile
     *
     * @return  self
     */
    public function setThumbnailFile($thumbnailFile): static
    {
        $this->thumbnailFile = $thumbnailFile;

        return $this;
    }


    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeImmutable $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

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

    public function getProject(): ?Project
    {
        return $this->project;
    }

    public function setProject(?Project $project): static
    {
        $this->project = $project;

        return $this;
    }
}

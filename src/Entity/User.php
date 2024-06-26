<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[UniqueEntity(fields: ['username'], message: 'There is already an account with this username')]
#[Vich\Uploadable]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups('users.index')]
    private ?int $id = null;

    #[ORM\Column(type: Types::STRING, length: 255, unique: true)]
    #[Groups('users.index')]
    private ?string $username = null;

    /**
     * @var list<string> The user roles
     */
    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column(type: Types::STRING, length: 180, unique: true)]
    #[Assert\Email]
    #[Groups('users.show')]
    private ?string $email = null;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true)]
    #[Groups('users.index', 'users.show')]
    private ?string $fullname = '';

    #[ORM\Column(type: Types::STRING, length: 255)]
    #[Groups('users.index')]
    private ?string $slug = '';

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true)]
    #[Groups('users.index', 'users.show')]
    private ?string $title = '';

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true)]
    #[Groups('users.index', 'users.show')]
    private ?string $subtitle = '';

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups('users.index', 'users.show')]
    private ?string $shortDescription = '';

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups('users.show')]
    private ?string $longDescription = '';

    #[ORM\Column(type: Types::BOOLEAN, nullable: true)]
    #[Groups('users.show')]
    private ?bool $isOpenToWork = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $updatedAt = null;


    #[ORM\Column(type: 'boolean')]
    private $isVisible = false;

    #[ORM\Column(type: 'boolean')]
    private $isVerified = false;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true)]
    #[Assert\Regex(
        pattern: '/^(0|\+33)[1-9]\d{8}$/',
        message: 'The phone number must be a valid French phone number.'
    )]
    #[Groups('users.show')]
    private ?string $tel = '';

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true)]
    #[Assert\URL]
    #[Groups('users.show')]
    private ?string $linkedin = '';

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true)]
    #[Assert\URL]
    private ?string $github = '';

    #[ORM\OneToMany(targetEntity: Experience::class, mappedBy: 'user', cascade: ['remove'])]
    private Collection $experiences;

    #[ORM\OneToMany(targetEntity: ScoreSkill::class, mappedBy: 'user', cascade: ['remove'])]
    private Collection $scoreSkills;

    #[ORM\OneToMany(targetEntity: Project::class, mappedBy: 'user', cascade: ['remove'])]
    private Collection $projects;

    #[ORM\OneToOne(mappedBy: 'user', cascade: ['persist', 'remove'])]
    private ?Media $media = null;

    public function __construct()
    {
        $this->updatedAt = new \DateTimeImmutable();
        $this->experiences = new ArrayCollection();
        $this->scoreSkills = new ArrayCollection();
        $this->projects = new ArrayCollection();
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): static
    {
        $this->username = $username;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->username;
    }

    /**
     * @see UserInterface
     *
     * @return list<string>
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        if ($this->isVerified()) {
            $roles[] = 'ROLE_VERIFIED';
        }

        return array_unique($roles);
    }

    /**
     * @param list<string> $roles
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }


    public function getFullname(): string
    {
        return $this->fullname;
    }

    public function setFullname(string $fullname): static
    {
        $this->fullname = $fullname;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): static
    {
        $this->slug = $slug;

        return $this;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(?string $title): static
    {
        $this->title = $title;

        return $this;
    }


    public function getSubtitle(): string
    {
        return $this->subtitle;
    }

    public function setSubtitle(?string $subtitle): static
    {
        $this->subtitle = $subtitle;

        return $this;
    }

    public function getShortDescription(): string
    {
        return $this->shortDescription;
    }

    public function setShortDescription(?string $shortDescription): static
    {
        $this->shortDescription = $shortDescription;

        return $this;
    }

    public function getLongDescription(): string
    {
        return $this->longDescription;
    }

    public function setLongDescription(?string $longDescription): static
    {
        $this->longDescription = $longDescription;

        return $this;
    }

    public function isIsOpenToWork(): ?bool
    {
        return $this->isOpenToWork;
    }

    public function setIsOpenToWork(?bool $isOpenToWork): static
    {
        $this->isOpenToWork = $isOpenToWork;

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



    public function isVerified(): bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(bool $isVerified): static
    {
        $this->isVerified = $isVerified;

        return $this;
    }

    /**
     * Get the value of isVisible
     */
    public function getIsVisible()
    {
        return $this->isVisible;
    }

    /**
     * Set the value of isVisible
     *
     * @return  self
     */
    public function setIsVisible($isVisible)
    {
        $this->isVisible = $isVisible;

        return $this;
    }



    /**
     * @return Collection<int, Experience>
     */
    public function getExperiences(): Collection
    {
        return $this->experiences;
    }

    public function addExperience(Experience $experience): static
    {
        if (!$this->experiences->contains($experience)) {
            $this->experiences->add($experience);
            $experience->setUser($this);
        }

        return $this;
    }

    public function removeExperience(Experience $experience): static
    {
        if ($this->experiences->removeElement($experience)) {
            // set the owning side to null (unless already changed)
            if ($experience->getUser() === $this) {
                $experience->setUser(null);
            }
        }

        return $this;
    }


    /**
     * @return Collection<int, ScoreSkill>
     */
    public function getScoreSkills(): Collection
    {
        return $this->scoreSkills;
    }

    public function addScoreSkill(ScoreSkill $scoreSkill): static
    {
        if (!$this->scoreSkills->contains($scoreSkill)) {
            $this->scoreSkills->add($scoreSkill);
            $scoreSkill->setUser($this);
        }

        return $this;
    }

    public function removeScoreSkill(ScoreSkill $scoreSkill): static
    {
        if ($this->scoreSkills->removeElement($scoreSkill)) {
            // set the owning side to null (unless already changed)
            if ($scoreSkill->getUser() === $this) {
                $scoreSkill->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Project>
     */
    public function getProjects(): Collection
    {
        return $this->projects;
    }

    public function addProject(Project $project): static
    {
        if (!$this->projects->contains($project)) {
            $this->projects->add($project);
            $project->setUser($this);
        }

        return $this;
    }

    public function removeProject(Project $project): static
    {
        if ($this->projects->removeElement($project)) {
            // set the owning side to null (unless already changed)
            if ($project->getUser() === $this) {
                $project->setUser(null);
            }
        }

        return $this;
    }

    public function getMedia(): ?Media
    {
        return $this->media;
    }

    public function setMedia(?Media $media): static
    {
        // unset the owning side of the relation if necessary
        if ($media === null && $this->media !== null) {
            $this->media->setUser(null);
        }

        // set the owning side of the relation if necessary
        if ($media !== null && $media->getUser() !== $this) {
            $media->setUser($this);
        }

        $this->media = $media;

        return $this;
    }

    public function getTel(): ?string
    {
        return $this->tel;
    }

    public function setTel(?string $tel): static
    {
        $this->tel = $tel;

        return $this;
    }

    public function getLinkedin(): ?string
    {
        return $this->linkedin;
    }

    public function setLinkedin(?string $linkedin): static
    {
        $this->linkedin = $linkedin;

        return $this;
    }

    public function getGithub(): ?string
    {
        return $this->github;
    }

    public function setGithub(?string $github): static
    {
        $this->github = $github;

        return $this;
    }
}

<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\SkillRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: SkillRepository::class)]
class Skill
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::STRING, length: 255)]
    #[Assert\NotBlank()]
    #[Assert\Length(min: 2, max: 100)]
    #[Groups(['skills.index', 'skills.create'])]
    private ?string $title = null;

    #[ORM\ManyToMany(targetEntity: User::class, inversedBy: 'skills')]
    private Collection $user;

    #[ORM\OneToMany(targetEntity: ScoreSkill::class, mappedBy: 'skill', cascade: ['persist', 'remove'])]
    private Collection $scoreSkills;

    public function __construct()
    {
        $this->user = new ArrayCollection();
        $this->scoreSkills = new ArrayCollection();
    }



    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getUser(): Collection
    {
        return $this->user;
    }

    public function addUser(User $user): static
    {
        if (!$this->user->contains($user)) {
            $this->user->add($user);
        }

        return $this;
    }

    public function removeUser(User $user): static
    {
        $this->user->removeElement($user);

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
            $scoreSkill->setSkill($this);
        }

        return $this;
    }

    public function removeScoreSkill(ScoreSkill $scoreSkill): static
    {
        if ($this->scoreSkills->removeElement($scoreSkill)) {
            // set the owning side to null (unless already changed)
            if ($scoreSkill->getSkill() === $this) {
                $scoreSkill->setSkill(null);
            }
        }

        return $this;
    }
}

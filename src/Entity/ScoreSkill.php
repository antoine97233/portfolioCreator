<?php

namespace App\Entity;

use App\Repository\ScoreSkillRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ScoreSkillRepository::class)]
class ScoreSkill
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    #[Assert\Range(
        min: 1,
        max: 5,
        notInRangeMessage: 'The score must be between {{ min }} and {{ max }}.'
    )]
    private ?int $score = null;

    #[ORM\ManyToOne(inversedBy: 'scoreSkills')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\ManyToOne(inversedBy: 'scoreSkills')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Skill $skill = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getScore(): ?int
    {
        return $this->score;
    }

    public function setScore(int $score): static
    {
        $this->score = $score;

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

    public function getSkill(): ?Skill
    {
        return $this->skill;
    }

    public function setSkill(?Skill $skill): static
    {
        $this->skill = $skill;

        return $this;
    }
}

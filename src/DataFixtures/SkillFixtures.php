<?php

namespace App\DataFixtures;

use App\Entity\Skill;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class SkillFixtures extends Fixture implements DependentFixtureInterface
{
    public function __construct()
    {
    }

    public function load(ObjectManager $manager): void
    {
        $this->loadSkills($manager);
    }

    public function loadSkills(ObjectManager $manager): void
    {

        $skills = [
            'HTML',
            'CSS',
            'JavaScript',
            'Python',
            'Java',
            'PHP',
            'Ruby',
            'Swift',
            'TypeScript',
            'C++',
            'Vue.js'
        ];

        foreach ($skills as $skillTitle) {
            $skill = new Skill();
            $skill->setTitle($skillTitle);
            $manager->persist($skill);
        }

        $manager->flush();
    }

    public function getDependencies()
    {
        return [UserFixtures::class];
    }
}

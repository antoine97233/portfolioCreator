<?php

namespace App\DataFixtures;

use App\Entity\Skill;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

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
        $faker = Factory::create();

        $skills = ['Bootstrap', 'HTML', 'Symfony', 'NestJS'];

        foreach ($skills as $skillTitle) {
            $skill = new Skill();
            $skill->setTitle($skillTitle);
            $manager->persist($skill);

            for ($i = 1; $i <= 20; $i++) {
                $this->getReference('USER' . $i)->addSkill($skill);
            }
        }

        $manager->flush();
    }

    public function getDependencies()
    {
        return [UserFixtures::class];
    }
}

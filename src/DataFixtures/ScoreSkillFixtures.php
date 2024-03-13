<?php

namespace App\DataFixtures;

use App\DataFixtures\UserFixtures;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class ScoreSkillFixtures extends Fixture implements DependentFixtureInterface
{
    public function __construct()
    {
    }

    public function load(ObjectManager $manager): void
    {
        $this->loadScoreSkills($manager);
    }

    public function loadScoreSkills(ObjectManager $manager): void
    {
        $faker = Factory::create();

        for ($i = 1; $i <= 20; $i++) {
            $user = $this->getReference('USER' . $i);

            // Assuming you have a list of skills (you can replace it with your own logic)
            $skills = $manager->getRepository(\App\Entity\Skill::class)->findAll();

            // Shuffle the array of skills to get a random order
            shuffle($skills);

            // Limit the number of skills to be assigned (between 2 and 5)
            $numSkills = mt_rand(2, 5);

            // Take only the first $numSkills from the shuffled array
            $selectedSkills = array_slice($skills, 0, $numSkills);

            foreach ($selectedSkills as $skill) {
                $scoreSkill = new \App\Entity\ScoreSkill();
                $scoreSkill->setUser($user)
                    ->setSkill($skill)
                    ->setScore($faker->numberBetween(1, 5)); // Adjust the range based on your scoring system

                $manager->persist($scoreSkill);
            }
        }

        $manager->flush();
    }

    public function getDependencies()
    {
        return [UserFixtures::class, SkillFixtures::class];
    }
}

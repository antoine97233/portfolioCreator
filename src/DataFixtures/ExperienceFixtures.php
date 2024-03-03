<?php

namespace App\DataFixtures;

use App\DataFixtures\UserFixtures;
use App\Entity\Experience;
use App\Entity\Task;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class ExperienceFixtures extends Fixture implements DependentFixtureInterface
{
    public function __construct()
    {
    }

    public function load(ObjectManager $manager): void
    {
        $this->loadExperiences($manager);
    }

    public function loadExperiences(ObjectManager $manager): void
    {
        $faker = Factory::create();

        for ($i = 1; $i <= 20; $i++) {
            $experience = new Experience();
            $experience->setTitle($faker->jobTitle)
                ->setLocation($faker->city)
                ->setStartDate($faker->dateTimeThisDecade)
                ->setEndDate($faker->dateTimeThisYear)
                ->setIsFormation($faker->boolean)
                ->setUser($this->getReference('USER' . $i));

            for ($j = 1; $j <= 5; $j++) {
                $task = new Task();
                $task->setDescription($faker->sentence)
                    ->setExperience($experience);

                $manager->persist($task);
            }

            $manager->persist($experience);
        }

        $manager->flush();
    }

    public function getDependencies()
    {
        return [UserFixtures::class];
    }
}

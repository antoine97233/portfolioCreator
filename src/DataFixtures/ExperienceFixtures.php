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
            for ($j = 1; $j <= 3; $j++) {
                // Créer 3 expériences avec isFormation à true
                $experienceTrue = $this->createExperience($faker, true, $i, $manager);
                $this->createTasks($faker, $experienceTrue, 4, $manager);

                // Créer 3 expériences avec isFormation à false
                $experienceFalse = $this->createExperience($faker, false, $i, $manager);
                $this->createTasks($faker, $experienceFalse, 4, $manager);
            }
        }

        $manager->flush();
    }

    private function createExperience($faker, $isFormation, $userId, $manager)
    {
        $experience = new Experience();
        $experience->setTitle($faker->jobTitle)
            ->setLocation($faker->city)
            ->setStartDate($faker->dateTimeThisDecade)
            ->setEndDate($faker->dateTimeThisYear)
            ->setIsFormation($isFormation)
            ->setUser($this->getReference('USER' . $userId));

        $manager->persist($experience);

        return $experience;
    }

    private function createTasks($faker, $experience, $numTasks, $manager)
    {
        for ($k = 1; $k <= $numTasks; $k++) {
            $task = new Task();
            $task->setDescription($faker->sentence)
                ->setExperience($experience);

            $manager->persist($task);
        }
    }

    public function getDependencies()
    {
        return [UserFixtures::class];
    }
}

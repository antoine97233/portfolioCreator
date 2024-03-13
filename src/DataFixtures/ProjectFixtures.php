<?php

namespace App\DataFixtures;

use App\DataFixtures\UserFixtures;
use App\Entity\Project;
use App\Entity\Task;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class ProjectFixtures extends Fixture implements DependentFixtureInterface
{
    public function __construct()
    {
    }

    public function load(ObjectManager $manager): void
    {
        $this->loadProjects($manager);
    }

    public function loadProjects(ObjectManager $manager): void
    {
        $faker = Factory::create();

        for ($i = 1; $i <= 20; $i++) {
            for ($j = 1; $j <= 4; $j++) {
                $project = new Project();
                $project->setTitle($faker->words(3, true))
                    ->setDescription($faker->sentence)
                    ->setUser($this->getReference('USER' . $i));

                for ($k = 1; $k <= 5; $k++) {
                    $task = new Task();
                    $task->setDescription($faker->sentence)
                        ->setProject($project);

                    $manager->persist($task);
                }

                $manager->persist($project);
            }
        }

        $manager->flush();
    }

    public function getDependencies()
    {
        return [UserFixtures::class];
    }
}

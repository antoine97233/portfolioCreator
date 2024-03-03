<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Faker\Factory;

class UserFixtures extends Fixture
{

    private UserPasswordHasherInterface $hasher;

    public function __construct(UserPasswordHasherInterface $hasher)
    {
        $this->hasher = $hasher;
    }

    public function load(ObjectManager $manager): void
    {
        $this->loadUsers($manager);
    }




    public function loadUsers(ObjectManager $manager): void
    {
        $faker = Factory::create();

        for ($i = 1; $i <= 20; $i++) {
            $user = new User();
            $user->setUsername($faker->userName)
                ->setEmail($faker->email)
                ->setFullname($faker->name)
                ->setTitle('Job title')
                ->setSubtitle('Job subtitle')
                ->setShortDescription($faker->sentence)
                ->setLongDescription($faker->paragraph)
                ->setSlug($faker->slug)
                ->setRoles([])
                ->setIsVisible(true)
                ->setIsVerified(true)
                ->setApiToken("user{$i}");

            $hashPassword = $this->hasher->hashPassword($user, "password");
            $user->setPassword($hashPassword);

            $this->addReference('USER' . $i, $user);

            $manager->persist($user);
        }

        $manager->flush();
    }
}

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
                ->setTitle('Developer ' . $faker->word)
                ->setSubtitle($faker->word . ' / ' . $faker->word)
                ->setShortDescription($faker->sentence)
                ->setIsOpenToWork($i <= 10)
                ->setLongDescription($faker->paragraph)
                ->setSlug($faker->slug)
                ->setRoles(['ROLE_USER'])
                ->setTel($faker->phoneNumber)
                ->setLinkedin($faker->url)
                ->setIsVisible(true)
                ->setIsVerified(true);

            $hashPassword = $this->hasher->hashPassword($user, "password");
            $user->setPassword($hashPassword);

            $this->addReference('USER' . $i, $user);

            $manager->persist($user);
        }

        $manager->flush();
    }
}

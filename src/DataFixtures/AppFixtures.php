<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
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


    public function loadUSers(ObjectManager $manager): void
    {
        // Create 10 users with fake information
        for ($i = 1; $i <= 10; $i++) {
            $user = new User();
            $user->setUsername("User{$i}")
                ->setEmail("user{$i}@example.com")
                ->setSlug("user-{$i}")
                ->setRoles([]);

            // Use a simple password for demo purposes
            $hashPassword = $this->hasher->hashPassword($user, "password");

            $user->setPassword($hashPassword);

            $manager->persist($user);
        }

        $manager->flush();
    }
}

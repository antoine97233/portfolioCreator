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


    private function loadUsers(ObjectManager $manager): void
    {
        $user = new User();
        $user->setFullname("Antoine Jolivet")
            ->setUsername("antoine")
            ->setEmail("antoine.jolivet29@gmail.com")
            ->setRoles(['ROLE_USER']);

        $hashPassword = $this->hasher->hashPassword(
            $user,
            "test"
        );

        $user->setPassword($hashPassword);

        $manager->persist($user);
        $manager->flush();
    }
}

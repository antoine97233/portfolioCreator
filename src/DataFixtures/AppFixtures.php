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
            ->setSlug("antoine-jolivet")
            ->setUsername("antoine")
            ->setEmail("antoine.jolivet29@gmail.com")
            ->setRoles(['ROLE_USER']);

        $hashPassword = $this->hasher->hashPassword(
            $user,
            "test"
        );

        $user->setPassword($hashPassword)
            ->setTitle("Développeur Fullstack")
            ->setSubtitle("Symfony / React")
            ->setShortDescription("Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.")
            ->setLongDescription("Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. 
            Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.")
            ->setIsOpenToWork(true);


        $user2 = new User();
        $user2->setFullname("Francis Henry")
            ->setSlug("francis-henry")
            ->setUsername("francis")
            ->setEmail("ant_972@hotmail.com")
            ->setRoles(['ROLE_USER']);

        $hashPassword2 = $this->hasher->hashPassword(
            $user2,
            "test"
        );

        $user2->setPassword($hashPassword2)
            ->setTitle("Développeur FrontEnd")
            ->setSubtitle("VueJs / React Native")
            ->setShortDescription("Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.")
            ->setLongDescription("Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. 
                Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.")
            ->setIsOpenToWork(false);

        $manager->persist($user2);
        $manager->persist($user);
        $manager->flush();
    }
}

<?php

namespace App\DataFixtures;

use App\Entity\Skill;
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
        $this->loadSkills($manager);
    }

    public function loadSkills(ObjectManager $manager): void
    {
        $skill1 = new Skill();
        $skill1->setTitle("Bootstrap");
        $skill2 = new Skill();
        $skill2->setTitle("HTML");
        $skill3 = new Skill();
        $skill3->setTitle("Symfony");
        $skill4 = new Skill();
        $skill4->setTitle("NestJS");

        $manager->persist($skill1);
        $manager->persist($skill2);
        $manager->persist($skill3);
        $manager->persist($skill4);


        $manager->flush();
    }


    public function loadUSers(ObjectManager $manager): void
    {
        // Create 10 users with fake information
        for ($i = 1; $i <= 10; $i++) {
            $user = new User();
            $user->setUsername("User{$i}")
                ->setEmail("user{$i}@example.com")
                ->setFullname("Nom Prenom{$i}")
                ->setUsername("username{$i}")
                ->setTitle('Title Post')
                ->setSubtitle('Subtitle Post')
                ->setShortDescription('Lorem ipsum dolor sit amet, consectetur adipiscing elit.')
                ->setLongDescription('Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum')
                ->setSlug("user-{$i}")
                ->setRoles([])
                ->setIsVisible(true)
                ->setIsVerified(true);;


            // Use a simple password for demo purposes
            $hashPassword = $this->hasher->hashPassword($user, "password");

            $user->setPassword($hashPassword);

            $manager->persist($user);
        }

        $manager->flush();
    }
}

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
        $this->loadAdmin($manager);
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
                ->setRoles(['ROLE_USER'])
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


    public function loadAdmin(ObjectManager $manager)
    {
        $userAdmin = new User();
        $userAdmin->setUsername("antoine")
            ->setEmail("antoine.jolivet29@gmail.com")
            ->setFullname("Antoine Jolivet")
            ->setTitle('Développeur full-stack')
            ->setSubtitle('React - Symfony')
            ->setShortDescription("Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.")
            ->setLongDescription("Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo. Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt. Neque porro quisquam est, qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit, sed quia non numquam eius modi tempora incidunt ut labore et dolore magnam aliquam quaerat voluptatem. Ut enim ad minima veniam, quis nostrum exercitationem ullam corporis suscipit laboriosam, nisi ut aliquid ex ea commodi consequatur? Quis autem vel eum iure reprehenderit qui in ea voluptate velit esse quam nihil molestiae consequatur, vel illum qui dolorem eum fugiat quo voluptas nulla pariatur?")
            ->setSlug("antoine-jolivet")
            ->setRoles(['ROLE_ADMIN'])
            ->setIsVisible(true)
            ->setIsVerified(true)
            ->setApiToken("antoine");

        $hashPassword = $this->hasher->hashPassword($userAdmin, "password");
        $userAdmin->setPassword($hashPassword);


        $manager->persist($userAdmin);
        $manager->flush();
    }
}

<?php

namespace App\DataFixtures;

use App\Entity\Role;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    public function __construct(private UserPasswordHasherInterface $userPasswordHasher)
    {
    }

    public function load(ObjectManager $manager): void
    {
        $this->createAdmin($manager);
        $manager->flush();
    }

    private function createAdmin(ObjectManager $manager)
    {
        $adminUser = $manager->getRepository(user::class)->findOneBy(['username' => 'admin']);
        if(is_null($adminUser)){
            $adminRole = $manager->getRepository(Role::class)->findOneBy(['name' => 'ROLE_ADMIN']);

            $admin = new User('Admin', 'Admin', 'admin', 'admin');
            $admin->register($this->userPasswordHasher, $adminRole);
            $admin->setActive(true);

            $manager->persist($admin);
        }
    }
}

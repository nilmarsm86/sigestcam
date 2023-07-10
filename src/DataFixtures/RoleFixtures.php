<?php

namespace App\DataFixtures;

use App\Entity\Role;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class RoleFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $roles = ['ROLE_USER', 'ROLE_TECHNICAL', 'ROLE_OFFICER', 'ROLE_BOSS', 'ROLE_ADMIN'];
        foreach ($roles  as $role){
            $rol = $manager->getRepository(Role::class)->findOneBy(['name' => $role]);
            if(is_null($rol)){
                $manager->persist(new Role($role));
            }
        }

        $manager->flush();
    }
}

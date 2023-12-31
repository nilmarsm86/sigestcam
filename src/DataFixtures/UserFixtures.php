<?php

namespace App\DataFixtures;

use App\Entity\Enums\State;
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
        $this->createUser($manager);
        $this->createTechnical($manager);
        $this->createOfficer($manager);
        $this->createBoss($manager);
        $this->createAdmin($manager);
        $this->createSuperAdmin($manager);
        $manager->flush();
    }

    /**
     * @param ObjectManager $manager
     * @param User $user
     * @param array $roles
     * @return void
     */
    private function save(ObjectManager $manager, User $user, array $roles): void
    {
        $user->register($this->userPasswordHasher, $roles[0]);
        foreach($roles as $role){
            if($role->getId() !== $roles[0]->getId()){
                $user->addRole($role);
            }
        }
        $user->setState(State::Active);

        $manager->persist($user);
    }

    /**
     * @param ObjectManager $manager
     * @return void
     */
    private function createSuperAdmin(ObjectManager $manager)
    {
        $adminUser = $manager->getRepository(User::class)->findOneBy(['username' => 'admin']);
        if(is_null($adminUser)){
            $roles = $manager->getRepository(Role::class)->findAll();

            $admin = new User('Super', 'Admin', 'superadmin', 'superadmin');
            $this->save($manager, $admin, $roles);
        }
    }

    /**
     * @param ObjectManager $manager
     * @return void
     */
    private function createAdmin(ObjectManager $manager)
    {
        $adminUser = $manager->getRepository(User::class)->findOneBy(['username' => 'admin']);
        if(is_null($adminUser)){
            $roles = $manager->getRepository(Role::class)->findAll();
            $roles = array_filter($roles, function ($role){
                return $role->getName() !== 'ROLE_SUPER_ADMIN';
            });

            $admin = new User('Admin', 'Admin', 'admin', 'admin');
            $this->save($manager, $admin, $roles);
        }
    }

    /**
     * @param ObjectManager $manager
     * @return void
     */
    private function createBoss(ObjectManager $manager)
    {
        $bossUser = $manager->getRepository(User::class)->findOneBy(['username' => 'boss']);
        if(is_null($bossUser)){
            $roles = $manager->getRepository(Role::class)->findAll();
            $roles = array_filter($roles, function ($role){
                return $role->getName() !== 'ROLE_SUPER_ADMIN' && $role->getName() !== 'ROLE_ADMIN';
            });

            $boss = new User('Boss', 'Boss', 'boss', 'boss');
            $this->save($manager, $boss, array_values($roles));
        }
    }

    /**
     * @param ObjectManager $manager
     * @return void
     */
    private function createOfficer(ObjectManager $manager)
    {
        $officerUser = $manager->getRepository(User::class)->findOneBy(['username' => 'officer']);
        if(is_null($officerUser)){
            $roles = $manager->getRepository(Role::class)->findAll();
            $roles = array_filter($roles, function ($role){
                return $role->getName() !== 'ROLE_SUPER_ADMIN' && $role->getName() !== 'ROLE_ADMIN' && $role->getName() !== 'ROLE_BOSS' && $role->getName() !== 'ROLE_TECHNICAL';
            });

            $officer = new User('Officer', 'Officer', 'officer', 'officer');
            $this->save($manager, $officer, array_values($roles));
        }
    }

    /**
     * @param ObjectManager $manager
     * @return void
     */
    private function createTechnical(ObjectManager $manager)
    {
        $technicalUser = $manager->getRepository(User::class)->findOneBy(['username' => 'technical']);
        if(is_null($technicalUser)){
            $roles = $manager->getRepository(Role::class)->findAll();
            $roles = array_filter($roles, function ($role){
                return $role->getName() !== 'ROLE_SUPER_ADMIN' && $role->getName() !== 'ROLE_ADMIN' && $role->getName() !== 'ROLE_BOSS' && $role->getName() !== 'ROLE_OFFICER';
            });

            $technical = new User('Technical', 'Technical', 'technical', 'technical');
            $this->save($manager, $technical, array_values($roles));
        }
    }

    /**
     * @param ObjectManager $manager
     * @return void
     */
    private function createUser(ObjectManager $manager)
    {
        $user = $manager->getRepository(User::class)->findOneBy(['username' => 'user']);
        if(is_null($user)){
            $role = $manager->getRepository(Role::class)->findOneBy(['name'=>'ROLE_USER']);

            $user = new User('User', 'User', 'user', 'user');
            $this->save($manager, $user, [$role]);
        }
    }

}

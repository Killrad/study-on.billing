<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

use App\Entity\User;
class StartUsers extends Fixture
{
    private $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        $user = new User();
        $user->setEmail('admin@example.test');
        $user->setRoles(['ROLE_SUPER_ADMIN']);
        $user->setBalance(1000);
        $testPassword = 'asdewq123';
        $hashedPassword = $this->passwordHasher->hashPassword(
            $user,
            $testPassword
        );
        $user->setPassword($hashedPassword);
        $manager->persist($user);

        $secondUser = new User();
        $secondUser->setEmail('common.user@example.test');
        $secondUser->setRoles(['ROLE_USER']);
        $secondUser->setBalance(50);
        $testPassword = 'asdewq123';
        $hashedPassword = $this->passwordHasher->hashPassword(
            $secondUser,
            $testPassword
        );
        $secondUser->setPassword($hashedPassword);
        $manager->persist($secondUser);
        $manager->flush();
    }
}

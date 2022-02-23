<?php

namespace App\DataFixtures;

use App\Entity\Ingredient;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    private UserPasswordHasherInterface $hasher;

    public function __construct(UserPasswordHasherInterface $hasher)
    {
        $this->hasher = $hasher;
    }

    public function load(ObjectManager $manager): void
    {
        $users = [
            [
                'firstName' => 'Carlos',
                'lastName' => 'Pereira De Amorim',
                'email' => 'carlos@shauri.fr',
                'password' => 'password',
                'birthday' => '1982-12-13',
                'roles' => ['ROLE_SUPER_ADMIN'],
            ]
        ];
        foreach ($users as $userArray) {
            $user = new User();
            $firstName = $userArray['firstName'];
            $user->setFirstName($firstName);
            $lastName = $userArray['lastName'];
            $user->setLastName($lastName);
            $email = $userArray['email'];
            $user->setEmail($email);
            $birthday = $userArray['birthday'];
            $user->setBirthday(new \DateTime($birthday));
            $passwordRaw = $userArray['password'];
            // hasher le mot de passe
            $password = $this->hasher->hashPassword($user, $passwordRaw);
            $user->setPassword($password);
            $roles = $userArray['roles'];
            $user->setRoles($roles);

            $manager->persist($user);
            $this->addReference($email, $user);
        }

        $manager->flush();
    }
}

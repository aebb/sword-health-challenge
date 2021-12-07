<?php

namespace App\Tests\Integration\Fixtures;

use App\Entity\Task;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class TestFixture extends Fixture
{
    protected array $records;

    public function __construct()
    {
        $this->records = [];
    }

    public function getRecords(): array
    {
        return $this->records;
    }

    public function load(ObjectManager $manager)
    {
        foreach ($this->records as $record) {
            $manager->persist($record);
        }

        $manager->flush();
    }

    public function addUser(
        UserPasswordHasherInterface $passwordHasher,
        $name = 'admin',
        string $password = 'admin',
        array $roles = ['ROLE_TECHNICIAN'],
        string $token = 'adminToken'
    ): TestFixture {
        $user = new User();

        $user->setUsername($name);
        $user->setRoles($roles);
        $user->setPassword(
            $passwordHasher->hashPassword(
                $user,
                $password
            )
        );
        $user->setApiToken($token);

        $this->records[] = $user;
        return $this;
    }

    public function addTask(User $user, string $text = 'dummy'): TestFixture
    {
        $task = new Task($user, $text);

        $this->records[] = $task;
        return $this;
    }
}

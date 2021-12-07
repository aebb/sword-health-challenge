<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * @codeCoverageIgnore
 */
class UserFixtures extends Fixture
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager)
    {

        $tech1 = new User();
        $tech1->setUsername('tech1');
        $tech1->setRoles([USER::ROLES['ROLE_TECHNICIAN']]);
        $tech1->setPassword(
            $this->passwordHasher->hashPassword(
                $tech1,
                'tech1pw'
            )
        );
        $tech1->setApiToken('tech1token');
        $manager->persist($tech1);

        $tech2 = new User();
        $tech2->setUsername('tech2');
        $tech2->setRoles([USER::ROLES['ROLE_TECHNICIAN']]);
        $tech2->setPassword(
            $this->passwordHasher->hashPassword(
                $tech2,
                'tech2pw'
            )
        );
        $tech2->setApiToken('tech2token');
        $manager->persist($tech2);

        $manager1 = new User();
        $manager1->setUsername('manager1');
        $manager1->setRoles([USER::ROLES['ROLE_MANAGER']]);
        $manager1->setPassword(
            $this->passwordHasher->hashPassword(
                $manager1,
                'manager1pw'
            )
        );
        $manager1->setApiToken('manager1token');
        $manager->persist($manager1);

        $manager->flush();
    }
}

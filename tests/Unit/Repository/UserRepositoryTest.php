<?php

namespace App\Tests\Unit\Repository;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use PHPUnit\Framework\TestCase;
use ReflectionProperty;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

/** @coversDefaultClass \App\Repository\UserRepository */
class UserRepositoryTest extends TestCase
{
    protected EntityManagerInterface $entityManager;
    protected UserRepository $sut;

    protected function setUp(): void
    {
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->sut = $this->getMockBuilder(UserRepository::class)
            ->disableOriginalConstructor()
            ->setMethodsExcept([
                'upgradePassword',
                'findAllManagers'
            ])
            ->getMock();

        $prop = new ReflectionProperty(UserRepository::class, '_em');
        $prop->setAccessible(true);
        $prop->setValue($this->sut, $this->entityManager);
    }

    /**
     * @covers::upgradePassword
     */
    public function testUpgradePasswordSuccess()
    {
        $model = new User();

        $this->entityManager->expects($this->once())->method('persist')->with($model);
        $this->entityManager->expects($this->once())->method('flush');

        $this->sut->upgradePassword($model, 'dummyPassword');
    }

    /**
     * @covers ::upgradePassword
     */
    public function testUpgradePasswordFailure()
    {
        $model = $this->createMock(PasswordAuthenticatedUserInterface::class);

        $this->expectException(UnsupportedUserException::class);

        $this->sut->upgradePassword($model, 'dummyPassword');
    }

    /**
     * @covers::findAllManagers
     */
    public function testFindAllManagers()
    {
        $expected = [$this->createMock(User::class)];
        $builder = $this->createMock(QueryBuilder::class);
        $query = $this->createMock(AbstractQuery::class);

        $builder->expects($this->once())
            ->method('where')
            ->with('u.roles LIKE :roles')
            ->willReturn($builder);

        $builder
            ->expects($this->once())
            ->method('setParameter')
            ->withConsecutive(['roles'], ['%"' . User::ROLES['ROLE_MANAGER'] . '"%'])
            ->willReturnOnConsecutiveCalls($builder, $builder);

        $builder->expects($this->once())
            ->method('getQuery')
            ->willReturn($query);

        $query->expects($this->once())
            ->method('execute')
            ->willReturn($expected);

        $this->sut->expects($this->once())
            ->method('createQueryBuilder')
            ->with('u')
            ->willReturn($builder);

        $this->assertEquals($expected, $this->sut->findAllManagers());
    }
}

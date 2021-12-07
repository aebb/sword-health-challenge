<?php

namespace App\Tests\Unit\Repository;

use App\Repository\RepositoryFactory;
use App\Repository\TaskRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \App\Repository\RepositoryFactory
*/
class RepositoryFactoryTest extends TestCase
{
    protected ManagerRegistry $registry;
    protected EntityManagerInterface $entityManager;
    protected RepositoryFactory $sut;

    protected function setUp(): void
    {
        $this->registry = $this->createMock(ManagerRegistry::class);
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->sut = $this->getMockBuilder(RepositoryFactory::class)
            ->setConstructorArgs([
                $this->registry,
                $this->entityManager,
            ])
            ->onlyMethods(['createRepository'])
            ->getMock();
    }


    /**
     * @covers ::__construct
     * @covers ::createRepository
     * @covers ::getTaskRepository
     */
    public function testTaskRepository()
    {
        $repository = $this->createMock(TaskRepository::class);

        $this->sut
            ->expects($this->once())
            ->method('createRepository')
            ->with(TaskRepository::class, $this->registry, $this->entityManager)
            ->willReturn($repository);

        $this->assertSame($repository, $this->sut->getTaskRepository());
    }

    /**
     * @covers ::__construct
     * @covers ::createRepository
     * @covers ::getUserRepository
     */
    public function testUserRepository()
    {
        $repository = $this->createMock(UserRepository::class);

        $this->sut
            ->expects($this->once())
            ->method('createRepository')
            ->with(UserRepository::class, $this->registry, $this->entityManager)
            ->willReturn($repository);

        $this->assertSame($repository, $this->sut->getUserRepository());
    }
}

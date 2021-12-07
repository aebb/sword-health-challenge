<?php

namespace App\Tests\Unit\Repository;

use App\Entity\Task;
use App\Repository\TaskRepository;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use ReflectionProperty;

/** @coversDefaultClass \App\Repository\TaskRepository */
class TaskRepositoryTest extends TestCase
{
    protected EntityManagerInterface $entityManager;
    protected TaskRepository $sut;

    protected function setUp(): void
    {
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->sut = $this->getMockBuilder(TaskRepository::class)
            ->disableOriginalConstructor()
            ->setMethodsExcept([
                'persist'
            ])
            ->getMock();

        $prop = new ReflectionProperty(TaskRepository::class, 'entityManager');
        $prop->setAccessible(true);
        $prop->setValue($this->sut, $this->entityManager);
    }

    /**
     * @covers ::persist
     */
    public function testPersist()
    {
        $model = $this->createMock(Task::class);

        $this->entityManager->expects($this->once())->method('persist')->with($model);
        $this->entityManager->expects($this->once())->method('flush');

        $this->assertSame($model, $this->sut->persist($model));
    }
}

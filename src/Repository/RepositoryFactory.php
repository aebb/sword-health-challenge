<?php

namespace App\Repository;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

class RepositoryFactory
{
    protected const REPOSITORY_TASK = 'task-repository';
    protected const REPOSITORY_USER = 'user-repository';

    protected ManagerRegistry $registry;
    protected EntityManagerInterface $entityManager;

    protected array $repositories;

    public function __construct(ManagerRegistry $registry, EntityManagerInterface $entityManager)
    {
        $this->registry = $registry;
        $this->entityManager = $entityManager;
    }

    /** @codeCoverageIgnore */
    protected function createRepository(
        string $repository,
        ManagerRegistry $registry,
        EntityManagerInterface $entityManager,
        ...$args
    ) {
        return new $repository($registry, $entityManager, ...$args);
    }

    public function getTaskRepository(): TaskRepository
    {
        if (!isset($this->repositories[self::REPOSITORY_TASK])) {
            $this->repositories[self::REPOSITORY_TASK] = $this->createRepository(
                TaskRepository::class,
                $this->registry,
                $this->entityManager
            );
        }
        return $this->repositories[self::REPOSITORY_TASK];
    }

    public function getUserRepository(): UserRepository
    {
        if (!isset($this->repositories[self::REPOSITORY_USER])) {
            $this->repositories[self::REPOSITORY_USER] = $this->createRepository(
                UserRepository::class,
                $this->registry,
                $this->entityManager
            );
        }
        return $this->repositories[self::REPOSITORY_USER];
    }
}

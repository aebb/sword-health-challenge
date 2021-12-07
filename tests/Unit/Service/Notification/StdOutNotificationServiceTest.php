<?php

namespace App\Tests\Unit\Service\Notification;

use App\Entity\User;
use App\Message\Task\TaskMessage;
use App\Repository\RepositoryFactory;
use App\Repository\UserRepository;
use App\Service\Notification\StdOutNotificationService;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

/**
 * @coversDefaultClass \App\Service\Notification\StdOutNotificationService
 */
class StdOutNotificationServiceTest extends TestCase
{
    /**
     * @covers ::__construct
     * @covers ::process
     */
    public function testProcess()
    {
        $logger            = $this->createMock(LoggerInterface::class);
        $repositoryFactory = $this->createMock(RepositoryFactory::class);
        $userRepository    = $this->createMock(UserRepository::class);

        $repositoryFactory
            ->expects($this->once())
            ->method('getUserRepository')
            ->willReturn($userRepository);

        $sut = new StdOutNotificationService($logger, $repositoryFactory);
        $logger->expects($this->exactly(2))->method('info');

        $user = new User();
        $user->setUsername('manager1');
        $userRepository
            ->expects($this->once())
            ->method('findAllManagers')
            ->willReturn([$user]);

        $message = $this->createMock(TaskMessage::class);
        $message->method('__toString')
            ->willReturn('dummy message');

        $sut->process($message);
    }
}

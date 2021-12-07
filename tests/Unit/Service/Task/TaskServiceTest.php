<?php

namespace App\Tests\Unit\Service\Task;

use App\Entity\Task;
use App\Entity\User;
use App\Message\Task\TaskMessage;
use App\Repository\RepositoryFactory;
use App\Repository\TaskRepository;
use App\Repository\UserRepository;
use App\Request\RequestModel;
use App\Request\Task\ListRequest;
use App\Request\Task\PostRequest;
use App\Service\Task\TaskService;
use App\Utils\AppException;
use Exception;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use ReflectionProperty;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;

/**
 * @coversDefaultClass \App\Service\Task\TaskService
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class TaskServiceTest extends TestCase
{
    private ListRequest $listingRequest;
    private PostRequest $postRequest;
    private LoggerInterface $logger;
    private RepositoryFactory $repositoryFactory;
    private UserRepository $userRepository;
    private TaskRepository $taskRepository;
    private MessageBusInterface $messageBus;
    private int $limit;
    private TaskService $sut;

    public function setUp(): void
    {
        parent::setUp();
        $this->logger     = $this->createMock(LoggerInterface::class);
        $this->repositoryFactory = $this->createMock(RepositoryFactory::class);
        $this->userRepository = $this->createMock(UserRepository::class);
        $this->taskRepository = $this->createMock(TaskRepository::class);
        $this->messageBus = $this->createMock(MessageBusInterface::class);
        $this->limit      = 5;
        $this->sut = new TaskService(
            $this->logger,
            $this->repositoryFactory,
            $this->messageBus,
            $this->limit
        );

        $this->listingRequest = $this->createMock(ListRequest::class);
        $this->listingRequest->method('getToken')->willReturn('dummy');

        $this->postRequest = $this->createMock(PostRequest::class);
        $this->postRequest->method('getToken')->willReturn('dummy');
        $this->postRequest->method('getSummary')->willReturn('dummy');

        $this->repositoryFactory
            ->method('getUserRepository')
            ->willReturn($this->userRepository);

        $this->repositoryFactory
            ->method('getTaskRepository')
            ->willReturn($this->taskRepository);
    }

    /**
     * @covers ::__construct
     */
    public function testConstruct()
    {
        $sut = new TaskService(
            $this->logger,
            $this->repositoryFactory,
            $this->messageBus,
            $this->limit
        );

        $prop = new ReflectionProperty(TaskService::class, 'logger');
        $prop->setAccessible(true);
        $this->assertEquals($this->logger, $prop->getValue($sut));

        $prop = new ReflectionProperty(TaskService::class, 'repositoryFactory');
        $prop->setAccessible(true);
        $this->assertEquals($this->repositoryFactory, $prop->getValue($sut));

        $prop = new ReflectionProperty(TaskService::class, 'messageBus');
        $prop->setAccessible(true);
        $this->assertEquals($this->messageBus, $prop->getValue($sut));

        $prop = new ReflectionProperty(TaskService::class, 'limit');
        $prop->setAccessible(true);
        $this->assertEquals($this->limit, $prop->getValue($sut));
    }

    /**
     * @covers::list
     * @covers::getUser
     * @throws Exception
     */
    public function testListingFailure()
    {
        $this->logger->expects($this->once())->method('info');
        $this->logger->expects($this->once())->method('error');

        $this->expectException(AppException::class);

        $this->sut->list($this->listingRequest);
    }

    /**
     * @covers::list
     * @covers::getUser
     * @throws Exception
     */
    public function testListingSuccess()
    {
        $expected = [];
        $this->logger->expects($this->exactly(2))->method('info');

        $user = new User();
        $this->userRepository
            ->expects($this->once())
            ->method('findOneBy')
            ->with(['apiToken' => $this->listingRequest->getToken()])
            ->willReturn($user);

        $this->taskRepository
            ->expects($this->once())
            ->method('findBy')
            ->with(['owner' => $user], null, $this->limit, 0)
            ->willReturn($expected);

        $this->assertEquals($expected, $this->sut->list($this->listingRequest));
    }

    /**
     * @throws Exception
     */
    public function testPostSuccess()
    {
        $this->logger->expects($this->exactly(2))->method('info');
        $expected = $this->createMock(Task::class);

        $user = new User();
        $this->userRepository
            ->expects($this->once())
            ->method('findOneBy')
            ->with(['apiToken' => $this->listingRequest->getToken()])
            ->willReturn($user);

        $this->taskRepository
            ->expects($this->once())
            ->method('persist')
            ->with($this->callback(function () {
                return true;
            }))
            ->willReturn($expected);

        $message = new TaskMessage($expected);
        $this->messageBus
            ->expects($this->once())
            ->method('dispatch')
            ->with($message)
            ->willReturn(new Envelope($message));

        $this->assertEquals($expected, $this->sut->post($this->postRequest));
    }
}

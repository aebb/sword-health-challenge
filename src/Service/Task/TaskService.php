<?php

namespace App\Service\Task;

use App\Entity\Task;
use App\Entity\User;
use App\Message\Task\TaskMessage;
use App\Repository\RepositoryFactory;
use App\Request\Task\ListRequest;
use App\Request\Task\PostRequest;
use App\Request\RequestModel;
use App\Utils\AbstractService;
use App\Utils\AppException;
use Exception;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;

class TaskService extends AbstractService
{
    public const USER_NOT_FOUND_MESSAGE = "User not found";

    private int $limit;

    private MessageBusInterface $messageBus;

    public function __construct(
        LoggerInterface $logger,
        RepositoryFactory $repositoryFactory,
        MessageBusInterface $messageBus,
        int $listLimit
    ) {
        parent::__construct($logger, $repositoryFactory);
        $this->messageBus = $messageBus;
        $this->limit      = $listLimit;
    }

    /**
     * @throws Exception
     */
    public function list(ListRequest $request): array
    {
        $this->logger->info(sprintf(self::LOG_MESSAGE_STARTED, $request->getRequest()->getBaseUrl()));

        $user     = $this->getUser($request);
        $limit    = min($request->getCount() ?? $this->limit, $this->limit);
        $offset   = $request->getStart() ?? 0;
        $criteria = $user->isManager() ? [] : ['owner' => $user];
        $tasks    = $this->repositoryFactory->getTaskRepository()->findBy($criteria, null, $limit, $offset);

        $this->logger->info(sprintf(self::LOG_MESSAGE_QUERY, implode($tasks)));
        return $tasks;
    }

    /**
     * @throws Exception
     */
    public function post(PostRequest $request): Task
    {
        $this->logger->info(sprintf(self::LOG_MESSAGE_STARTED, $request->getRequest()->getBaseUrl()));

        $user = $this->getUser($request);
        $result = $this->repositoryFactory->getTaskRepository()->persist(new Task($user, $request->getSummary()));

        $this->messageBus->dispatch(new TaskMessage($result));
        $this->logger->info(sprintf(self::LOG_MESSAGE_QUERY, $result));

        return $result;
    }

    /**
     * @throws Exception
     */
    private function getUser(RequestModel $requestModel): User
    {
        $token = $requestModel->getToken();
        $user = $this->repositoryFactory->getUserRepository()->findOneBy(['apiToken' => $token]);
        if (!$user) {
            $this->logger->error(self::LOG_MESSAGE_ERROR . $token);
            throw new AppException(self::USER_NOT_FOUND_MESSAGE, Response::HTTP_NOT_FOUND);
        }
        return $user;
    }
}

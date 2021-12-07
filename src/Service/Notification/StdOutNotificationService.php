<?php

namespace App\Service\Notification;

use App\Entity\User;
use App\Message\MessageInterface;
use App\Message\NotificationInterface;
use App\Repository\RepositoryFactory;
use App\Utils\AbstractService;
use Psr\Log\LoggerInterface;

class StdOutNotificationService extends AbstractService implements NotificationInterface
{
    private const NOTIFICATION_MESSAGE = "Hello %s! You have a new notification from %s";

    public function __construct(LoggerInterface $logger, RepositoryFactory $repositoryFactory)
    {
        parent::__construct($logger, $repositoryFactory);
    }

    public function process(MessageInterface $message): void
    {
        $this->logger->info(sprintf(self::LOG_MESSAGE_STARTED, $message));

        $managers = $this->repositoryFactory->getUserRepository()->findAllManagers();
        /**
         * @var User $manager
         * send a notification to every manager
         */
        foreach ($managers as $manager) {
            echo sprintf(self::NOTIFICATION_MESSAGE, $manager->getUserIdentifier(), $message);
        }

        $this->logger->info(sprintf(self::LOG_MESSAGE_FINISH, $message));
    }
}

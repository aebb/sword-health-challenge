<?php

namespace App\Message\Task;

use App\Message\NotificationInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class TaskMessageNotificationHandler implements MessageHandlerInterface
{
    private NotificationInterface $notificationService;

    public function __construct(NotificationInterface $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    public function __invoke(TaskMessage $message): void
    {
        $this->notificationService->process($message);
    }
}

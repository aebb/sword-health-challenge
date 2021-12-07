<?php

namespace App\Tests\Unit\Message;

use App\Message\NotificationInterface;
use App\Message\Task\TaskMessage;
use App\Message\Task\TaskMessageNotificationHandler;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \App\Message\Task\TaskMessageNotificationHandler
 */
class TaskMessageNotificationHandlerTest extends TestCase
{

    /**
     * @covers::__construct
     * @covers::__invoke
     */
    public function testInvoke()
    {
        $taskMessage = $this->createMock(TaskMessage::class);
        $notificationService = $this->createMock(NotificationInterface::class);
        $notificationService->expects($this->once())->method('process')->with($taskMessage);
        $sut = new TaskMessageNotificationHandler($notificationService);
        $sut->__invoke($taskMessage);
    }
}

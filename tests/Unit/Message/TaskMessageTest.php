<?php

namespace App\Tests\Unit\Message;

use App\Entity\Task;
use App\Entity\User;
use App\Message\Task\TaskMessage;
use PHPUnit\Framework\TestCase;
use ReflectionProperty;

/**
 * @coversDefaultClass \App\Message\Task\TaskMessage
 */
class TaskMessageTest extends TestCase
{
    /**
     * @covers ::__construct
     * @covers ::getTask
     * @covers ::__toString
     */
    public function testTaskMessage()
    {

        $username = 'dummy';
        $user = new User();
        $user->setUsername($username);

        $summary = 'dummy text';

        $task = new Task($user, $summary);

        $prop = new ReflectionProperty(Task::class, 'createdAt');
        $prop->setAccessible(true);
        $date = $prop->getValue($task);

        $asString = sprintf('%s - %s - %s', $username, $summary, $date->format('Y-m-d H:i:s'));

        $taskMessage = new TaskMessage($task);

        $this->assertEquals($task, $taskMessage->getTask());
        $this->assertEquals($asString, $taskMessage->__toString());
    }
}

<?php

namespace App\Message\Task;

use App\Entity\Task;
use App\Message\MessageInterface;

class TaskMessage implements MessageInterface
{
    private Task $task;

    public function __construct(Task $task)
    {
        $this->task = $task;
    }

    public function getTask(): Task
    {
        return $this->task;
    }

    public function __toString(): string
    {
        return $this->getTask()->__toString();
    }
}

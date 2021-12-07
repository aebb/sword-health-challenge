<?php

namespace App\Tests\Unit\Entity;

use App\Entity\Task;
use App\Entity\User;
use PHPUnit\Framework\TestCase;
use ReflectionProperty;

/**
 * @coversDefaultClass \App\Entity\Task
 */
class TaskTest extends TestCase
{
    /**
     * @covers ::__construct
     * @covers ::getId
     * @covers ::getSummary
     * @covers ::getOwner
     * @covers ::getCreatedAt
     * @covers ::__toString
     * @covers ::jsonSerialize
     */
    public function testTask()
    {

        $username = 'dummy';
        $user = new User();
        $user->setUsername($username);

        $summary = 'dummy text';

        $model = new Task($user, $summary);

        $id = 50;
        $prop = new ReflectionProperty(Task::class, 'id');
        $prop->setAccessible(true);
        $prop->setValue($model, $id);

        $prop = new ReflectionProperty(Task::class, 'createdAt');
        $prop->setAccessible(true);
        $date = $prop->getValue($model);

        $json = [
            'id' => $id,
            'owner' => $username,
            'summary' => $summary,
            'createdAt' => $date->format('Y-m-d H:i:s')
        ];
        $asString = sprintf('%s - %s - %s', $username, $summary, $date->format('Y-m-d H:i:s'));

        $this->assertEquals($id, $model->getId());
        $this->assertEquals($user, $model->getOwner());
        $this->assertEquals($summary, $model->getSummary());
        $this->assertEquals($date, $model->getCreatedAt());

        $this->assertEquals($json, $model->jsonSerialize());
        $this->assertEquals($asString, $model->__toString());
    }
}

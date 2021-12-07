<?php

namespace App\Tests\Integration\Task;

use App\Tests\Integration\EndpointTester;
use App\Tests\Integration\Fixtures\TestFixture;
use Symfony\Component\HttpFoundation\Response;

/** @coversDefaultClass \App\Controller\ */
class ListEndpointTest extends EndpointTester
{

    public function testExecuteListNoAuth()
    {
        $this->client->request(
            'GET',
            '/tasks',
            [],
            [],
            ['HTTP_X-AUTH-TOKEN' => 'fake-token']
        );

        $response = $this->client->getResponse();
        $this->assertEquals(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
        $this->assertEquals(['error' => 'Invalid credentials.'], json_decode($response->getContent(), true));
    }

    public function testExecuteListBadRequest()
    {
        $fixtures = new TestFixture();
        $fixtures->addUser($this->passwordHasher);
        $this->loadFixture($fixtures);

        $this->client->request(
            'GET',
            '/tasks?start=x&count=x',
            [],
            [],
            ['HTTP_X-AUTH-TOKEN' => $fixtures->getRecords()[0]->getApiToken()]
        );
        $response = $this->client->getResponse();
        $responseBody = json_decode($response->getContent(), true);

        $expected = [
            'error' => 'start parameter must be a positive integer & count parameter must be a positive integer'
        ];

        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
        $this->assertEquals($expected, $responseBody);
    }

    public function testExecuteListManagerAll()
    {
        $fixtures = $this->loadSamples();
        $this->client->request(
            'GET',
            '/tasks',
            [],
            [],
            ['HTTP_X-AUTH-TOKEN' => $fixtures->getRecords()[2]->getApiToken()]
        );
        $response = $this->client->getResponse();
        $responseBody = json_decode($response->getContent(), true);

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertCount(5, $responseBody);
        $this->validateResponse($responseBody);
    }

    public function testExecuteListManagerFilter()
    {
        $fixtures = $this->loadSamples();
        $this->client->request(
            'GET',
            '/tasks?start1&count=3',
            [],
            [],
            ['HTTP_X-AUTH-TOKEN' => $fixtures->getRecords()[2]->getApiToken()]
        );
        $response = $this->client->getResponse();
        $responseBody = json_decode($response->getContent(), true);

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertCount(3, $responseBody);
        $this->validateResponse($responseBody);
    }

    public function testExecuteListTechAll()
    {
        $fixtures = $this->loadSamples();
        $this->client->request(
            'GET',
            '/tasks',
            [],
            [],
            ['HTTP_X-AUTH-TOKEN' => $fixtures->getRecords()[0]->getApiToken()]
        );
        $response = $this->client->getResponse();
        $responseBody = json_decode($response->getContent(), true);

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertCount(3, $responseBody);
        $this->validateResponse($responseBody);
    }

    public function testExecuteListTechFilter()
    {
        $fixtures = $this->loadSamples();
        $this->client->request(
            'GET',
            '/tasks?start=1&count=1',
            [],
            [],
            ['HTTP_X-AUTH-TOKEN' => $fixtures->getRecords()[0]->getApiToken()]
        );
        $response = $this->client->getResponse();
        $responseBody = json_decode($response->getContent(), true);

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertCount(1, $responseBody);
        $this->validateResponse($responseBody);
    }

    private function validateResponse(array $response)
    {
        foreach ($response as $entry) {
            $this->assertCount(4, $entry);
            $this->assertIsInt($entry['id']);
            $this->assertIsString($entry['owner']);
            $this->assertIsString($entry['summary']);
            $this->assertTrue((bool) strtotime($entry['createdAt']));
        }
    }

    private function loadSamples(): TestFixture
    {
        $fixtures = new TestFixture();
        $fixtures->addUser(
            $this->passwordHasher,
            'user1',
            'user1pw',
            ['ROLE_TECHNICIAN'],
            'user1token',
        );

        $fixtures->addUser(
            $this->passwordHasher,
            'user2',
            'user2pw',
            ['ROLE_TECHNICIAN'],
            'user2token',
        );

        $fixtures->addUser(
            $this->passwordHasher,
            'manager1',
            'manager1pw',
            ['ROLE_MANAGER'],
            'manager1token',
        );

        $user1    = $fixtures->getRecords()[0];
        $user2    = $fixtures->getRecords()[1];

        $fixtures->addTask($user1, 'task1-user1');
        $fixtures->addTask($user1, 'task2-user1');
        $fixtures->addTask($user1, 'task3-user1');

        $fixtures->addTask($user2, 'task1-user1');
        $fixtures->addTask($user2, 'task2-user2');
        $fixtures->addTask($user2, 'task3-user2');

        $this->loadFixture($fixtures);

        return $fixtures;
    }
}

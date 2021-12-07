<?php

namespace App\Tests\Integration\Task;

use App\Tests\Integration\EndpointTester;
use App\Tests\Integration\Fixtures\TestFixture;
use Symfony\Component\HttpFoundation\Response;

/** @coversDefaultClass \App\Controller\ */
class PostEndpointTest extends EndpointTester
{

    public function testExecutePostNoAuth()
    {
        $this->client->request(
            'POST',
            '/tasks',
            [],
            [],
            ['HTTP_X-AUTH-TOKEN' => 'fake-token']
        );

        $response = $this->client->getResponse();
        $this->assertEquals(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
        $this->assertEquals(['error' => 'Invalid credentials.'], json_decode($response->getContent(), true));
    }

    public function testExecutePostBadRequest()
    {
        $fixtures = new TestFixture();
        $fixtures->addUser($this->passwordHasher);
        $this->loadFixture($fixtures);

        $this->client->request(
            'POST',
            '/tasks',
            [],
            [],
            ['HTTP_X-AUTH-TOKEN' => $fixtures->getRecords()[0]->getApiToken()]
        );

        $response = $this->client->getResponse();
        $responseBody = json_decode($response->getContent(), true);

        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
        $this->assertEquals(['error' => 'summary parameter must be present'], $responseBody);
    }

    public function testExecutePostSuccess()
    {
        $fixtures = new TestFixture();
        $fixtures->addUser($this->passwordHasher);
        $this->loadFixture($fixtures);

        $user = $fixtures->getRecords()[0];
        $summary = 'dummy';

        $this->client->request(
            'POST',
            '/tasks',
            [],
            [],
            ['HTTP_X-AUTH-TOKEN' => $user->getApiToken()],
            json_encode(['summary' => $summary])
        );

        $response = $this->client->getResponse();
        $responseBody = json_decode($response->getContent(), true);

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertCount(4, $responseBody);
        $this->assertEquals(1, $responseBody['id']);
        $this->assertEquals($user->getUserIdentifier(), $responseBody['owner']);
        $this->assertEquals($summary, $responseBody['summary']);
        $this->assertTrue((bool) strtotime($responseBody['createdAt']));

        $this->assertCount(1, $this->transport->getSent());
    }
}

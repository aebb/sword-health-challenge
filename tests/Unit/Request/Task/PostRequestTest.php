<?php

namespace App\Tests\Unit\Request\Task;

use App\Request\Task\PostRequest;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * @coversDefaultClass \App\Request\Task\PostRequest
 * @covers \App\Request\RequestModel
 */
class PostRequestTest extends TestCase
{
    /**
     * @covers ::__construct
     * @covers ::getSummary
     * @covers ::getRequest
     * @covers ::getToken
     */
    public function testConstruct()
    {
        $request = $this->createMock(Request::class);
        $body = '{"summary":"dummy"}';
        $authorizationToken = 'foo-bar';

        $params = $this->createMock(ParameterBagInterface::class);
        $params
            ->expects($this->once())
            ->method('get')
            ->with('X-AUTH-TOKEN')
            ->willReturn($authorizationToken);
        $request->headers = $params;


        $request->expects($this->once())->method('getContent')->willReturn($body);

        $sut = new PostRequest($request);

        $this->assertEquals(json_decode($body, true)['summary'], $sut->getSummary());
        $this->assertEquals($authorizationToken, $sut->getToken());
        $this->assertEquals($request, $sut->getRequest());
    }
}

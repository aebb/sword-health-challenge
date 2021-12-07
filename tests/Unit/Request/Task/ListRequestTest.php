<?php

namespace App\Tests\Unit\Request\Task;

use App\Request\Task\ListRequest;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * @coversDefaultClass \App\Request\Task\ListRequest
 * @covers \App\Request\RequestModel
 */
class ListRequestTest extends TestCase
{
    /**
     * @covers ::__construct
     * @covers ::getRequest
     * @covers ::getToken
     * @covers ::getStart
     * @covers ::getCount
     */
    public function testConstructWithNullValues()
    {
        $request = $this->createMock(Request::class);

        $params = $this->createMock(ParameterBagInterface::class);
        $params
            ->expects($this->once())
            ->method('get')
            ->with('X-AUTH-TOKEN')
            ->willReturn('');
        $request->headers = $params;

        $params = $this->createMock(ParameterBagInterface::class);
        $params
            ->expects($this->exactly(2))
            ->method('get')
            ->withConsecutive(['start'], ['count'])
            ->willReturnOnConsecutiveCalls(null, null);
        $request->query = $params;

        $model = new ListRequest($request);

        $this->assertEmpty($model->getToken());
        $this->assertNull($model->getStart());
        $this->assertNull($model->getCount());
        $this->assertEquals($request, $model->getRequest());
    }

    /**
     * @covers ::__construct
     * @covers ::getRequest
     * @covers ::getToken
     * @covers ::getStart
     * @covers ::getCount
     */
    public function testConstruct()
    {
        $authorizationToken = '__dummy_token__';
        $start = 123;
        $count = 456;

        $request = $this->createMock(Request::class);

        $params = $this->createMock(ParameterBagInterface::class);
        $params
            ->expects($this->once())
            ->method('get')
            ->with('X-AUTH-TOKEN')
            ->willReturn($authorizationToken);
        $request->headers = $params;

        $params = $this->createMock(ParameterBagInterface::class);
        $params
            ->expects($this->exactly(2))
            ->method('get')
            ->withConsecutive(['start'], ['count'])
            ->willReturnOnConsecutiveCalls($start, $count);
        $request->query = $params;


        $model = new ListRequest($request);

        $this->assertEquals($authorizationToken, $model->getToken());
        $this->assertEquals($start, $model->getStart());
        $this->assertEquals($count, $model->getCount());
        $this->assertEquals($request, $model->getRequest());
    }
}

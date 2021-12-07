<?php

namespace App\Tests\Unit\Controller;

use App\Controller\Task\Controller;
use App\Service\Task\TaskService;
use App\Utils\AppException;
use App\Utils\RequestValidator;
use Exception;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @coversDefaultClass \App\Controller\Task\Controller
 */
class ControllerTest extends TestCase
{
    private RequestValidator $validator;

    private TaskService $service;

    private Request $request;

    private Controller $sut;

    public function setUp(): void
    {
        $this->validator = $this->createMock(RequestValidator::class);
        $this->service   = $this->createMock(TaskService::class);
        $this->request   = new Request();
        $this->request->headers->set('X-AUTH-TOKEN', 'foobar');
        $this->sut = $this->getMockBuilder(Controller::class)
            ->setConstructorArgs([ $this->validator, $this->service])
            ->onlyMethods(['json'])
            ->getMock();
    }

    /**
     * @covers::__construct
     * @covers::executeList
     */
    public function testListAppException()
    {
        $expected = new AppException('dummy-error', 300);
        $this->validator->method('process')->willThrowException($expected);

        $this->sut
            ->expects($this->once())
            ->method('json')
            ->willReturnCallback(function () use ($expected) {
                return new JsonResponse(['error' => $expected->getMessage()], $expected->getCode());
            });

        $result = $this->sut->executeList($this->request);

        $this->assertEquals(json_encode(['error' => $expected->getMessage()]), $result->getContent());
        $this->assertEquals($expected->getCode(), $result->getStatusCode());
    }

    /**
     * @covers::__construct
     * @covers::executePost
     */
    public function testPostAppException()
    {
        $expected = new AppException('dummy-error', 300);
        $this->validator->method('process')->willThrowException($expected);

        $this->sut
            ->expects($this->once())
            ->method('json')
            ->willReturnCallback(function () use ($expected) {
                return new JsonResponse(['error' => $expected->getMessage()], $expected->getCode());
            });

        $result = $this->sut->executePost($this->request);

        $this->assertEquals(json_encode(['error' => $expected->getMessage()]), $result->getContent());
        $this->assertEquals($expected->getCode(), $result->getStatusCode());
    }

    /**
     * @covers::__construct
     * @covers::executeList
     */
    public function testListSystemException()
    {
        $expected = new Exception('dummy-error', 300);
        $this->validator->method('process')->willThrowException($expected);

        $this->sut
            ->expects($this->once())
            ->method('json')
            ->willReturnCallback(function () use ($expected) {
                return new JsonResponse(
                    ['error' => Controller::UNEXPECTED_EXCEPTION],
                    Response::HTTP_INTERNAL_SERVER_ERROR
                );
            });

        $result = $this->sut->executeList($this->request);

        $this->assertEquals(
            json_encode(['error' =>  Controller::UNEXPECTED_EXCEPTION]),
            $result->getContent()
        );
        $this->assertEquals(Response::HTTP_INTERNAL_SERVER_ERROR, $result->getStatusCode());
    }

    /**
     * @covers::__construct
     * @covers::executePost
     */
    public function testPostSystemException()
    {
        $expected = new Exception('dummy-error', 300);
        $this->validator->method('process')->willThrowException($expected);

        $this->sut
            ->expects($this->once())
            ->method('json')
            ->willReturnCallback(function () use ($expected) {
                return new JsonResponse(
                    ['error' => Controller::UNEXPECTED_EXCEPTION],
                    Response::HTTP_INTERNAL_SERVER_ERROR
                );
            });

        $result = $this->sut->executePost($this->request);

        $this->assertEquals(
            json_encode(['error' =>  Controller::UNEXPECTED_EXCEPTION]),
            $result->getContent()
        );
        $this->assertEquals(Response::HTTP_INTERNAL_SERVER_ERROR, $result->getStatusCode());
    }
}

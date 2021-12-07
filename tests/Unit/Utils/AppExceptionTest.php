<?php

namespace App\Tests\Unit\Utils;

use App\Utils\AppException;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Response;

/**
 * @coversDefaultClass \App\Utils\AppException
 */
class AppExceptionTest extends TestCase
{
    /**
     * @covers ::__construct
     */
    public function testConstruct()
    {
        $message = 'dummy';
        $sut = new AppException($message);
        $this->assertEquals($message, $sut->getMessage());
        $this->assertEquals(Response::HTTP_BAD_REQUEST, $sut->getCode());
    }
}

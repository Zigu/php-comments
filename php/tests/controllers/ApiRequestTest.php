<?php declare(strict_types=1);

use PHPUnit\Framework\TestCase;

final class ApiRequestTest extends TestCase
{
    private const SAMPLE_URI = '/comments';

    public function testGetRequestMethod(): void
    {
        $request = new ApiRequest('GET', self::SAMPLE_URI);

        $this->assertSame('GET', $request->getRequestMethod());
    }

    public function testGetId(): void
    {
        $request1 = new ApiRequest('GET', self::SAMPLE_URI);
        $request2 = new ApiRequest('GET', self::SAMPLE_URI.'/5');

        $this->assertNull($request1->getId());
        $this->assertSame('5', $request2->getId());
    }

    public function testGetResourceName(): void
    {
        $request1 = new ApiRequest('GET', self::SAMPLE_URI);
        $request2 = new ApiRequest('GET', '/');

        $this->assertSame('comments', $request1->getResourceName());
        $this->assertSame('', $request2->getResourceName());
    }

    public function testHasResourceName(): void
    {
        $request1 = new ApiRequest('GET', self::SAMPLE_URI);
        $request2 = new ApiRequest('GET', '/');

        $this->assertTrue($request1->hasResourceName());
        $this->assertFalse($request2->hasResourceName());
    }
}

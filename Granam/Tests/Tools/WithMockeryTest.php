<?php
namespace Granam\Tests\Tools;

class WithMockeryTest extends TestWithMockery
{
    /**
     * @test
     */
    public function I_can_use_mockery_expectations_now()
    {
        $mock = $this->mockery('\DateTime');
        $mock->shouldReceive('foo')
            ->once()
            ->andReturn('bar');

        /** @noinspection PhpUndefinedMethodInspection */
        self::assertSame('bar', $mock->foo());
    }

    /**
     * @test
     */
    public function I_can_use_mockery_expectations_again()
    {
        $mock = $this->mockery('\DateTime');
        $mock->shouldReceive('foo')
            ->once()
            ->andReturn('bar');

        /** @noinspection PhpUndefinedMethodInspection */
        self::assertSame('bar', $mock->foo());
    }

    /**
     * @test
     */
    public function I_can_mock_even_interface()
    {
        $mock = $this->mockery('\Traversable');
        $mock->shouldReceive('foo')
            ->once()
            ->andReturn('bar');

        /** @noinspection PhpUndefinedMethodInspection */
        self::assertSame('bar', $mock->foo());
    }
}

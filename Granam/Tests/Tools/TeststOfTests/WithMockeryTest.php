<?php
namespace Granam\Tests\Tools\TestsOfTests;

use Granam\Tests\Tools\TestWithMockery;

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

    /**
     * @test
     */
    public function I_get_type_test()
    {
        $traversableTypeFromClassName = $this->type('\Traversable');
        $traversable = new \ArrayObject();
        self::assertInstanceOf('\Traversable', $traversable);
        self::assertTrue($traversableTypeFromClassName->match($traversable));

        $traversableTypeFromObject = $this->type($traversable);
        self::assertTrue($traversableTypeFromObject->match($traversable));

        $floatType = $this->type('float');
        $float = 1.2;
        self::assertTrue($floatType->match($float));
        $int = 1;
        self::assertFalse($floatType->match($int));

        $arrayType = $this->type([]);
        $array = [];
        self::assertTrue($arrayType->match($array));
    }

    /**
     * @test
     */
    public function I_can_get_sut_class_from_current_test()
    {
        self::assertSame(preg_replace('~Test$~', '', str_replace('\Tests\\', '\\', __CLASS__)), self::getSutClass());
        self::assertSame(
            preg_replace('~Test$~', '', str_replace('\Tests\\', '\\', __CLASS__)),
            self::getSutClass(__CLASS__)
        );
    }

    /**
     * @test
     */
    public function I_can_get_sut_class_from_any_class()
    {
        self::assertSame(
            \DateTime::class,
            self::getSutClass(\DateTime::class)
        );
        self::assertSame(
            'DT',
            self::getSutClass(\DateTime::class, '~([A-Z])[a-z]+~')
        );
    }
}

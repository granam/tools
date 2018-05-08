<?php
namespace Granam\Tests\Tools\TestsOfTests;

use Granam\Tests\Tools\TestWithMockery;
use Mockery\MockInterface;

class WithTestingFrameworkTest extends TestWithMockery
{
    /**
     * @test
     */
    public function I_can_use_mockery_expectations_now()
    {
        /** @var MockInterface|\DateTime $mock */
        $mock = $this->mockery(\DateTime::class);
        $mock->expects('getTimestamp')
            ->andReturn('bar');

        self::assertSame('bar', $mock->getTimestamp());
    }

    /**
     * @test
     */
    public function I_can_use_mockery_expectations_again()
    {
        /** @var MockInterface|\DateTime $mock */
        $mock = $this->mockery(\DateTime::class);
        $mock->expects('getTimestamp')
            ->andReturn('baz');

        self::assertSame('baz', $mock->getTimestamp());
    }

    /**
     * @test
     */
    public function I_can_mock_even_interface()
    {
        /** @var MockInterface|\IteratorAggregate $mock */
        $mock = $this->mockery(\IteratorAggregate::class);
        $mock->expects('getIterator')
            ->andReturn('bar');

        self::assertSame('bar', $mock->getIterator());
    }

    /**
     * @test
     */
    public function I_get_type_test()
    {
        $traversableTypeFromClassName = $this->type(\Traversable::class);
        $traversable = new \ArrayObject();
        self::assertInstanceOf(\Traversable::class, $traversable);
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

    /**
     * @test
     * @expectedException \Granam\Tests\Tools\Exceptions\MockingOfNonExistingMethod
     * @expectedExceptionMessageRegExp ~resetGregorianCalendar~
     */
    public function I_can_not_mock_non_existing_method_by_default()
    {
        $mock = $this->mockery(\DateTime::class);
        $mock->allows('resetGregorianCalendar');
    }

    /**
     * @test
     */
    public function I_can_mock_non_existing_method_if_desired()
    {
        $mock = $this->weakMockery(\DateTime::class);
        $mock->expects('resetGregorianCalendar')
            ->andReturn(0);
        /** @noinspection PhpUndefinedMethodInspection */
        self::assertSame(0, $mock->resetGregorianCalendar());
    }

    /**
     * @test
     * @depends I_can_mock_non_existing_method_if_desired
     * @expectedException \Granam\Tests\Tools\Exceptions\MockingOfNonExistingMethod
     * @expectedExceptionMessageRegExp ~resetGregorianCalendar~
     */
    public function I_can_not_mock_non_existing_method_by_default_again()
    {
        $mock = $this->mockery(\DateTime::class);
        $mock->allows('resetGregorianCalendar');
    }

}

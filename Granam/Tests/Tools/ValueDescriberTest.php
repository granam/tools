<?php
namespace Granam\Tools;

use PHPUnit\Framework\TestCase;

class ValueDescriberTest extends TestCase
{

    /**
     * @test
     */
    public function I_can_describe_scalar_and_null()
    {
        self::assertSame('123', ValueDescriber::describe(123));
        self::assertSame('123.456', ValueDescriber::describe(123.456));
        self::assertSame("'foo'", ValueDescriber::describe('foo'));
        self::assertSame('NULL', ValueDescriber::describe(null));
        self::assertSame('true', ValueDescriber::describe(true));
    }

    /**
     * @test
     */
    public function I_can_describe_object()
    {
        self::assertSame('instance of \stdClass', ValueDescriber::describe(new \stdClass()));
        $value = 'foo';
        self::assertSame(
            'instance of \\' . __NAMESPACE__ . '\ToStringObject ' . "($value)",
            ValueDescriber::describe(new ToStringObject($value))
        );
        self::assertSame(
            'instance of \DateTime (2016-11-15T12:45:02+01:00)',
            ValueDescriber::describe(new \DateTime('2016-11-15 12:45:02+01:00'))
        );
    }

    /**
     * @test
     */
    public function I_can_describe_array_and_resource()
    {
        self::assertSame('array', ValueDescriber::describe([]));
        self::assertSame('resource', ValueDescriber::describe(tmpfile()));
    }

    /**
     * @test
     * @dataProvider provideVariableValues
     *
     * @param string $expectedResult
     * @param mixed $value1
     * @param mixed $value2
     */
    public function I_can_describe_multiple_values($expectedResult, $value1, $value2)
    {
        $values = func_get_args();
        array_shift($values);
        self::assertSame($expectedResult, call_user_func_array(['\Granam\Tools\ValueDescriber', 'describe'], $values));
    }

    /**
     * @codeCoverageIgnore
     * @return array
     */
    public function provideVariableValues()
    {
        return [
            ["123,123.45,'foo',true,NULL,array,resource", 123, 123.45, 'foo', true, null, ['bar'], tmpfile()],
            ["123,123.45,'123','123.45',instance of \\stdClass", 123, 123.45, '123', '123.45', new \stdClass()],
        ];
    }
}

/** inner */
class ToStringObject
{

    private $value;

    public function __construct($value)
    {
        $this->value = $value;
    }

    public function __toString()
    {
        return (string)$this->value;
    }
}
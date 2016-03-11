<?php
namespace Granam\Tools;

class ValueDescriberTest extends \PHPUnit_Framework_TestCase
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
        self::assertSame('instance of stdClass', ValueDescriber::describe(new \stdClass()));
        $value = 'foo';
        self::assertSame(
            'instance of ' . __NAMESPACE__ . '\ToStringObject ' . "($value)",
            ValueDescriber::describe(new ToStringObject($value))
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

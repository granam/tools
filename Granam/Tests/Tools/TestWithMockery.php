<?php
namespace Granam\Tests\Tools;

abstract class TestWithMockery extends \PHPUnit_Framework_TestCase
{
    protected function tearDown()
    {
        \Mockery::close();
    }

    /**
     * @param string $className
     * @return \Mockery\MockInterface
     */
    protected function mockery($className)
    {
        self::assertTrue(
            class_exists($className) || interface_exists($className),
            "Given class $className does not exists."
        );

        return \Mockery::mock($className);
    }

    /**
     * @param mixed $expected
     * @return \Mockery\Matcher\Type
     */
    protected function type($expected)
    {
        return \Mockery::type($this->getTypeOf($expected));
    }

    /**
     * @param $value
     * @return string
     */
    private function getTypeOf($value)
    {
        if (is_string($value)) {
            return $value; // not type of "string" but direct description - like class name
        }
        if (is_object($value)) {
            return get_class($value);
        }

        return gettype($value);
    }

    /**
     * Expects test class with name \Granam\Tests\Tools\TestWithMockery therefore extended by \Tests sub-namespace
     * and Test suffix
     *
     * @return string|TestWithMockery
     */
    protected function getSutClass()
    {
        return preg_replace('~\\\Tests(.+)Test$~', '$1', get_class($this));
    }
}
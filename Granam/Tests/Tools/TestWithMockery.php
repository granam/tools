<?php
namespace Granam\Tests\Tools;

use PHPUnit\Framework\TestCase;

abstract class TestWithMockery extends TestCase
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
     * @param string $sutTestClass
     * @param string $regexp
     * @return string|TestWithMockery
     */
    protected static function getSutClass($sutTestClass = null, $regexp = '~\\\Tests(.+)Test$~')
    {
        return preg_replace($regexp, '$1', $sutTestClass ?: get_called_class());
    }
}
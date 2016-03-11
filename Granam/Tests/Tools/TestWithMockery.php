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
}

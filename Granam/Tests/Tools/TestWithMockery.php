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
        return \Mockery::mock($className);
    }
}

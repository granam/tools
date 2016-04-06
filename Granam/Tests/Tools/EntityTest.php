<?php
namespace Granam\Tests\Tools;

class EntityTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function I_can_use_entity()
    {
        $reflection = new \ReflectionClass('\Granam\Tools\Entity');
        self::assertTrue(
            $reflection->isInterface(),
            'Expected \Granam\Tools\Entity to be interface'
        );
        $methods = $reflection->getMethods();
        self::assertCount(1, $methods, 'Expected just a single method');
        /** @var \ReflectionMethod $method */
        $method = current($methods);
        self::assertSame('getId', $method->getName(), "Expected 'getId' method name");
    }
}
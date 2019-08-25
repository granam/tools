<?php declare(strict_types=1);

namespace Granam\Tests\Tools;

use Mockery\Generator\CachingGenerator;
use Mockery\Generator\StringManipulationGenerator;
use Mockery\Matcher\Type;
use Mockery\MockInterface;
use PHPUnit\Framework\TestCase;

abstract class TestWithMockery extends TestCase
{

    /** @var bool */
    private $strict = true;
    /** @var StringManipulationGenerator|null */
    private static $strictGenerator;

    protected function setUp(): void
    {
        if (!self::$strictGenerator) {
            self::$strictGenerator = StringManipulationGenerator::withDefaultPasses();
            self::$strictGenerator->addPass(new CalledMethodExistsPass());
            \Mockery::setGenerator(new CachingGenerator(self::$strictGenerator));
        }
    }

    protected function tearDown(): void
    {
        if (!$this->strict) {
            \Mockery::setGenerator(new CachingGenerator(self::$strictGenerator));
            $this->strict = true;
        }
        \Mockery::close();
    }

    /**
     * @param string $className
     * @param array $constructorArguments
     * @return \Mockery\MockInterface
     */
    protected function mockery(...$args): MockInterface
    {
        $className = $args[0];
        self::assertTrue(
            \class_exists($className) || \interface_exists($className),
            "Given class $className does not exists."
        );

        return \Mockery::mock(...$args);
    }

    /**
     * @param string $className
     * @param array $constructorArguments
     * @return MockInterface
     */
    protected function weakMockery(...$args): MockInterface
    {
        $this->strict = false;
        \Mockery::setGenerator(new CachingGenerator(StringManipulationGenerator::withDefaultPasses()));

        return $this->mockery(...$args);
    }

    /**
     * @param mixed $expected
     * @return \Mockery\Matcher\Type
     */
    protected function type($expected): Type
    {
        return \Mockery::type($this->getTypeOf($expected));
    }

    /**
     * @param mixed $value
     * @return string
     */
    private function getTypeOf($value): string
    {
        if (\is_string($value)) {
            return $value; // not type of "string" but direct description - like class name
        }
        if (\is_object($value)) {
            return \get_class($value);
        }

        return \gettype($value);
    }

    /**
     * Expects test class with name \Granam\Tests\Tools\TestWithMockery therefore extended by \Tests sub-namespace
     * and Test suffix
     *
     * @param string $sutTestClass
     * @param string $regexp
     * @return string|TestWithMockery
     */
    protected static function getSutClass(string $sutTestClass = null, string $regexp = '~\\\Tests(.+)Test$~'): string
    {
        return \preg_replace($regexp, '$1', $sutTestClass ?: static::class);
    }
}
<?php declare(strict_types=1);

namespace Granam\Tests\Tools;

use Composer\Autoload\ClassLoader;
use DrdPlus\RulesSkeleton\Web\WebPartsContainer;
use Granam\WebContentBuilder\Web\WebFiles;

class TestWithMockeryTest extends TestWithMockery
{

    /**
     * @test
     */
    public function I_can_mock_class_with_required_parameters()
    {
        $mock = $this->mockery(TestClassWithSomeRequiredParameterInConstructor::class);
        self::assertInstanceOf(TestClassWithSomeRequiredParameterInConstructor::class, $mock);
    }

    /**
     * @test
     */
    public function I_can_create_partial_mock_with_constructor_arguments()
    {
        $dateTime = new \DateTime('2018-01-01 01:01:01');
        $dateTimeMock = $this->mockery(\DateTime::class, [$dateTime->format('c')]);
        $dateTimeMock->makePartial();
        self::assertSame($dateTime->format('c'), $dateTimeMock->format('c'));
    }
}

class TestClassWithSomeRequiredParameterInConstructor
{
    public function __construct(\stdClass $foo, \stdClass $bar)
    {
    }
}
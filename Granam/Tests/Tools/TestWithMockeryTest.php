<?php declare(strict_types=1);

namespace Granam\Tests\Tools;

class TestWithMockeryTest extends TestWithMockery
{
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
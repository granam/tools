<?php /** @noinspection PhpDocMissingThrowsInspection */
declare(strict_types=1);
/** be strict for parameter types, https://www.quora.com/Are-strict_types-in-PHP-7-not-a-bad-idea */

namespace Granam\Tests\Tools;

use Mockery\Generator\MockConfiguration;
use Mockery\Generator\StringManipulation\Pass\Pass;

class CalledMethodExistsPass implements Pass
{
    public function apply($code, MockConfiguration $config): string
    {
        $guardMockedMethodsExists = $this->getCodeOfGuardMockedMethodsExists();
        $code = \preg_replace(
            '(\spublic function shouldReceive\(...\$methodNames\)\s*\{(\s+))',
            '$0' . $guardMockedMethodsExists . '$1',
            $code
        );

        return $code;
    }

    /**
     * Gives code of @see \Granam\Tests\Tools\CalledMethodExistsPass::guardMockedMethodsExists method
     * @return string
     */
    protected function getCodeOfGuardMockedMethodsExists(): string
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        $reflectionClass = new \ReflectionClass(static::class);
        $guardMockedMethodsExists = $reflectionClass->getMethod('guardMockedMethodsExists');
        $startLine = $guardMockedMethodsExists->getStartLine();
        $endLine = $guardMockedMethodsExists->getEndLine();
        $classCode = \file_get_contents($reflectionClass->getFileName());
        $classLines = \preg_split('~(\r|\n|\r\n|\n\r)~', $classCode);
        $methodLines = \array_splice($classLines, $startLine, $endLine - $startLine);
        //
        if (\trim(\reset($methodLines)) === '{') {
            \array_shift($methodLines); // removes first row
        }
        if (\trim(\end($methodLines)) === '}') {
            \array_pop($methodLines); // removes last row
        }

        return \implode(PHP_EOL, $methodLines);
    }

    /**
     * Have to use same variable as @see \Mockery\Mock::shouldReceive
     * @param array $methodNames
     */
    protected function guardMockedMethodsExists(array $methodNames): void
    {
        $testedInterface = \get_parent_class($this);
        $testedInterfaces = [];
        if ($testedInterface) {
            $testedInterfaces[] = $testedInterface;
        } else {
            /** @noinspection PhpUnhandledExceptionInspection */
            $mockReflection = new \ReflectionClass($this);
            foreach ($mockReflection->getInterfaces() as $interface) {
                if (\is_a($interface->getName(), \Mockery\MockInterface::class, true)) {
                    continue;
                }
                $testedInterfaces[] = $interface->getName();
            }
        }
        foreach ($methodNames as $methodName) {
            foreach ($testedInterfaces as $testedInterface) {
                /** @noinspection PhpUnhandledExceptionInspection */
                $testedInterfaceReflection = new \ReflectionClass($testedInterface);
                if ($testedInterfaceReflection->hasMethod($methodName)) {
                    continue 2; // next method to check
                }
            }
            /** @noinspection PhpUnnecessaryFullyQualifiedNameInspection */
            throw new \Granam\Tests\Tools\Exceptions\MockingOfNonExistingMethod(
                "Method '{$methodName}' does not exist on tested object '"
                . \implode(',', $testedInterfaces) . "'"
                . ' (use weakMockery() method if you really need to mock it)'
            );
        }
    }
}
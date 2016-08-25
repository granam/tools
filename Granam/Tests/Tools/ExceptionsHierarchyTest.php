<?php
namespace Granam\Tests\Tools;

use Granam\Tests\Exceptions\Tools\AbstractExceptionsHierarchyTest;

class ExceptionsHierarchyTest extends AbstractExceptionsHierarchyTest
{
    protected function getTestedNamespace()
    {
        return $this->getRootNamespace();
    }

    protected function getRootNamespace()
    {
        return str_replace('\Tests', '', __NAMESPACE__);
    }

    protected function getExceptionClassesSkippedFromUsageTest()
    {
        return [
            '\Granam\Tools\Exceptions\FileUpload',
        ];
    }

}
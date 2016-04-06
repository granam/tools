<?php
namespace Granam\Tests\Tools\TestsOfTests;

use Doctrine\ORM\EntityManager;
use Granam\Tests\Tools\AbstractDoctrineEntitiesTest;
use Granam\Tests\Tools\TestsOfTests\Entities\SomeEntity;
use Granam\Tools\Entity;

class PositiveTestOfAbstractDoctrineEntitiesTest extends AbstractDoctrineEntitiesTest
{
    protected function getDirsWithEntities()
    {
        return [
            __DIR__ . DIRECTORY_SEPARATOR . 'Entities'
        ];
    }

    protected function getExpectedEntityClasses()
    {
        return [
            '\Granam\Tests\Tools\TestsOfTests\SomeEntity',
        ];
    }

    protected function createEntitiesToPersist()
    {
        return [
            new SomeEntity('foo'),
        ];
    }

    protected function fetchEntitiesByOriginals(array $originalEntities, EntityManager $entityManager)
    {
        /** @var Entity $original */
        $original = current($originalEntities);
        $repository = $entityManager->getRepository('\Granam\Tests\Tools\TestsOfTests\SomeEntity');
        $fetched = $repository->find($original->getId());

        return [
            $fetched
        ];
    }

}

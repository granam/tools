<?php
namespace Granam\Tests\Tools;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Console\ConsoleRunner;
use Doctrine\ORM\Tools\Setup;
use Granam\Tools\Entity;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Tests\Fixtures\DummyOutput;

abstract class AbstractDoctrineEntitiesTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Symfony\Component\Console\Application */
    private $application;

    /** @var  EntityManager */
    private $entityManager;

    /** @var string */
    private $proxiesUniqueTempDir;

    protected function setUp()
    {
        if (!extension_loaded($this->getSqlExtensionName())) {
            self::markTestSkipped("The {$this->getSqlExtensionName()} extension is not available.");
        }

        $paths = $this->getDirsWithEntities();
        $config = Setup::createAnnotationMetadataConfiguration($paths, true /* dev mode */);
        $cache = new \Doctrine\Common\Cache\ArrayCache();
        $config->setMetadataCacheImpl($cache);
        $config->setQueryCacheImpl($cache);
        $driver = new \Doctrine\ORM\Mapping\Driver\AnnotationDriver(
            new \Doctrine\Common\Annotations\AnnotationReader(),
            $paths
        );
        $config->setMetadataDriverImpl($driver);
        $this->entityManager = EntityManager::create(
            [
                'driver' => $this->getSqlExtensionName(),
                'path' => ':memory:',
            ],
            $config
        );

        $helperSet = ConsoleRunner::createHelperSet($this->entityManager);
        $this->application = \Doctrine\ORM\Tools\Console\ConsoleRunner::createApplication($helperSet);
        $this->application->setAutoExit(false);
    }

    /**
     * @return string[]|array
     */
    abstract protected function getDirsWithEntities();

    protected function getSqlExtensionName()
    {
        return 'pdo_sqlite';
    }

    protected function tearDown()
    {
        $this->entityManager->getConnection()->close();
        if ($this->proxiesUniqueTempDir !== null) {
            $this->removeDirectory($this->proxiesUniqueTempDir);
        }
    }

    private function removeDirectory($dir)
    {
        foreach (scandir($dir) as $folder) {
            if ($folder === '.' || $folder === '..') {
                continue;
            }
            $folderFullPath = $dir . DIRECTORY_SEPARATOR . $folder;
            unlink($folderFullPath);
        }
        rmdir($dir);
    }

    /**
     * @test
     */
    public function I_can_persist_and_fetch_entities()
    {
        $this->I_can_create_schema();
        $proxies = $this->I_can_generate_proxies();

        foreach ($originalEntities = $this->createEntitiesToPersist() as $entityToPersist) {
            $this->entityManager->persist($entityToPersist);
        }
        $this->entityManager->flush();

        $originalGroupedByClass = $this->groupByClass($originalEntities);
        $originalGrouped = $this->groupById($originalGroupedByClass); // ids are set to entities after flush

        $this->entityManager->clear(); // clear disconnects all entities, but also by them containing entities

        $fetchedEntities = $this->fetchEntitiesByOriginals($originalEntities, $this->entityManager);
        self::assertCount(
            $countOfOriginals = count($originalEntities),
            $fetchedEntities,
            "Expected {$countOfOriginals} fetched entities (same as originals)"
        );
        $fetchedGroupedByClass = $this->groupByClass($fetchedEntities);
        $fetchedGrouped = $this->groupById($fetchedGroupedByClass);

        $usedProxies = [];
        foreach ($originalGrouped as $className => $originalGroup) {
            self::assertContains(
                $proxyName = $this->assembleProxyNameByClass($className),
                $proxies,
                "Proxy of name {$proxyName} has not been generated for class {$className}"
            );
            $usedProxies[] = $proxyName;
            self::assertArrayHasKey(
                $className,
                $fetchedGrouped,
                "Fetched entities miss any of {$className}"
            );
            $fetchedGroup = $fetchedGrouped[$className];
            foreach ($originalGroup as $id => $original) {
                self::assertArrayHasKey(
                    $id,
                    $fetchedGroup,
                    "Fetched entities of class {$className} miss entity of ID {$id}"
                );
                $fetched = $fetchedGroup[$id];
                self::assertEquals(
                    $original,
                    $fetched,
                    'Persisted and fetched-back entity should has same content'
                );
                self::assertNotSame(
                    $original,
                    $fetched,
                    'After clearing the unit of work the fetched back entity should not be the very same instance'
                );
            }
        }
        self::assertCount(
            0,
            $unusedProxies = array_diff($proxies, $usedProxies),
            'Entities of following proxies have not been tested for persistence: ' . implode(',', $unusedProxies)
        );

        $this->I_can_drop_schema();
    }

    private function groupByClass(array $entities)
    {
        $grouped = [];
        foreach ($entities as $entity) {
            $class = get_class($entity);
            if (!array_key_exists($class, $grouped)) {
                $grouped[$class] = [];
            }
            $grouped[$class][] = $entity;
        }

        return $grouped;
    }

    private function groupById(array $entities)
    {
        $grouped = [];
        foreach ($entities as $className => $entityGroup) {
            if (!array_key_exists($className, $grouped)) {
                $grouped[$className] = [];
            }
            foreach ($entityGroup as $entity) {
                if (!is_callable([$entity, 'getId'])) {
                    throw new \LogicException(
                        "Entity of class {$className} should has ID getter 'getId'"
                    );
                }
                /** @var Entity $entity */
                if (array_key_exists($entity->getId(), $grouped[$className])) {
                    throw new \RuntimeException(
                        "Two entities of the very same class {$className} have also same id {$entity->getId()};"
                        . ' their hashes are ' . spl_object_hash($grouped[$className][$entity->getId()])
                        . ' and ' . spl_object_hash($entity)
                    );
                }
                $grouped[$className][$entity->getId()] = $entity;
            }
        }

        return $grouped;
    }

    private function I_can_create_schema()
    {
        $exitCode = $this->application->run(new StringInput('orm:schema-tool:create'), $output = new DummyOutput());
        self::assertSame(0, $exitCode, $output->fetch());
    }

    private function I_can_generate_proxies()
    {
        $exitCode = $this->application->run(
            new StringInput('orm:generate:proxies ' . $this->getProxiesUniqueTempDir()),
            $output = new DummyOutput()
        );
        self::assertSame(0, $exitCode, $output->fetch());

        $proxyFileNames = array_merge( // rebuilding array to reset keys
            array_filter(
                scandir($this->getProxiesUniqueTempDir()),
                function ($folderName) {
                    return $folderName !== '.' && $folderName !== '..';
                }
            )
        );

        $expectedProxyFileNames = [];
        foreach ((array)$this->getExpectedEntityClasses() as $expectedEntityClass) {
            $expectedProxyFileNames[] = $this->assembleProxyNameByClass($expectedEntityClass);
        }

        self::assertEquals(
            $expectedProxyFileNames,
            $proxyFileNames,
            'Generated proxies do not match to expected ones'
        );

        return $expectedProxyFileNames;
    }

    protected function assembleProxyNameByClass($class)
    {
        return '__CG__' . str_replace('\\', '', $class) . '.php';
    }

    /**
     * @return string|string[]|array
     */
    abstract protected function getExpectedEntityClasses();

    /**
     * @return array|Entity[]
     */
    abstract protected function createEntitiesToPersist();

    /**
     * @param array $originalEntities
     * @param EntityManager $entityManager
     * @return array|Entity[]
     */
    abstract protected function fetchEntitiesByOriginals(array $originalEntities, EntityManager $entityManager);

    /**
     * @return string
     */
    private function getProxiesUniqueTempDir()
    {
        if ($this->proxiesUniqueTempDir === null) {
            $this->proxiesUniqueTempDir = sys_get_temp_dir() . '/' . uniqid('orm-proxies-test-', true);
        }

        return $this->proxiesUniqueTempDir;
    }

    private function I_can_drop_schema()
    {
        $exitCode = $this->application->run(new StringInput('orm:schema-tool:drop --force'), $output = new DummyOutput());
        self::assertSame(0, $exitCode, $output->fetch());
    }
}

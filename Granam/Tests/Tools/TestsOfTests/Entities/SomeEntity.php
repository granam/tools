<?php
namespace Granam\Tests\Tools\TestsOfTests\Entities;

use Granam\Tools\Entity;

/**
 * @\Doctrine\ORM\Mapping\Entity()
 */
class SomeEntity implements Entity
{
    /**
     * @var int
     * @\Doctrine\ORM\Mapping\Id
     * @\Doctrine\ORM\Mapping\Column(type="integer")
     * @\Doctrine\ORM\Mapping\GeneratedValue()
     */
    private $id;

    /**
     * @var string
     * @\Doctrine\ORM\Mapping\Column(type="string")
     */
    private $value;

    /**
     * @return string
     */
    public static function getClass()
    {
        return get_called_class();
    }

    public function __construct($value)
    {
        $this->value = (string)$value;
    }

    public function getId()
    {
        return $this->id;
    }
}
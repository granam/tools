<?php
namespace Granam\Tools;

class ValueDescriber
{
    /**
     * @param mixed $value
     *
     * @return string
     */
    public static function describe($value)
    {
        if (is_scalar($value) || is_null($value)) {
            return var_export($value, true);
        }

        if (is_object($value)) {
            $description = 'instance of ' . get_class($value);
            if (is_callable(array($value, '__toString'))) {
                $description .= ' (' . $value . ')';
            }

            return $description;
        }

        return gettype($value);
    }
}

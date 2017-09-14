<?php
namespace Granam\Tools;

class ValueDescriber
{
    /**
     * @param mixed $value ...
     *
     * @return string
     */
    public static function describe($value): string
    {
        if (func_num_args() === 1) {
            return self::describeSingleValue($value);
        }

        return implode(
            ',',
            array_map(
                function ($value) {
                    return self::describeSingleValue($value);
                },
                func_get_args()
            )
        );
    }

    private static function describeSingleValue($value): string
    {
        if (is_scalar($value) || $value === null) {
            return (string)var_export($value, true);
        }
        if (is_array($value)) {
            return var_export($value, true) ?? 'array';
        }

        if (is_object($value)) {
            $description = 'instance of \\' . get_class($value);
            if (method_exists($value, '__toString') && is_callable([$value, '__toString'])) {
                $description .= ' (' . $value . ')';
            } else if ($value instanceof \DateTime) {
                $description .= ' (' . $value->format(DATE_ATOM) . ')';
            }

            return $description;
        }

        return gettype($value);
    }
}
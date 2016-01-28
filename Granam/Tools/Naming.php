<?php
namespace Granam\Tools;

class Naming
{
    /**
     * @param string $value
     * @return string
     */
    public static function camelCaseClassToSnakeCase($value)
    {
        if (!preg_match('~[\\\]?(?<basename>\w+)$~', $value, $matches)) {
            return strtolower($value);
        }
        $baseName = $matches['basename'];
        $parts = preg_split('~([A-Z][a-z_]*)~', $baseName, -1, PREG_SPLIT_DELIM_CAPTURE|PREG_SPLIT_NO_EMPTY);
        $underscored = preg_replace('~_{2,}~', '_', implode('_', $parts));
        $snake_case = strtolower($underscored);

        return $snake_case;
    }
}
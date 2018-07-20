<?php
namespace Granam\Tools;

use Granam\Strict\Object\StrictObject;

class DirName extends StrictObject
{
    // TODO fix this for files (not dirs only) and for solving two dots in any part of path
    public static function getPathWithResolvedParents(string $folder): string
    {
        $withoutTrailingParents = \preg_replace('~([\\/][.]{2})*$~', '', $folder);
        $parentsCount = (int)((\strlen($folder) - \strlen($withoutTrailingParents)) / 3);
        while ($parentsCount > 0 && $withoutTrailingParents !== '') {
            $withoutTrailingParents = \dirname($withoutTrailingParents, 1);
            $parentsCount--;
            [$withoutTrailingParents, $newParentsCount] = self::getDirWithoutTrailingParentsAndTheirCount($withoutTrailingParents);
            $parentsCount += $newParentsCount;
        }

        return \basename($withoutTrailingParents);
    }

    private static function getDirWithoutTrailingParentsAndTheirCount(string $dir): array
    {
        $withoutTrailingParents = \preg_replace('~([\\/][.]{2})*$~', '', $dir);
        $parentsCount = (int)((\strlen($dir) - \strlen($withoutTrailingParents)) / 3);

        return [$withoutTrailingParents, $parentsCount];
    }
}
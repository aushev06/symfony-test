<?php

namespace EDM\Storages;

abstract class AbstractImageStorage
{
    public static function composeFilename(string $base, string $name, string $extension = 'jpg'): string
    {
        return $base . $name . '.' . $extension;
    }

    public static function composeFilenames(string $base, array $names, string $extension = 'jpg'): array
    {
        $result = [];
        foreach ($names as $key => $name) {
            if (is_array($name)) {
                $result[is_int($key) ? ($name[0] ?? $key) : $key] = static::composeFilename($base, $name[0] ?? '', $name[1] ?? $extension);
            } else {
                $result[is_int($key) ? $name : $key] = static::composeFilename($base, $name, $extension);
            }
        }
        return $result;
    }
}

<?php

abstract class BaseExtender
{
    /**
     * Check whether ext options include target ext
     *
     * @param string $targetExt
     * @param array  $options
     *
     * @return bool
     */
    protected final function includeExt(array $options, string $targetExt): bool
    {
        if (in_array(static::EXTEND_ALL, $options)) {
            return true;
        }
        if (in_array($targetExt, $options)) {
            return true;
        }
        return false;
    }

    abstract public function extend(array $data, int $userId, int $teamId, array $extensions = []): array;

    abstract public function extendMulti(array $data, int $userId, int $teamId, array $extensions = []): array;
}

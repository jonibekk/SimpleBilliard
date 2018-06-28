<?php
/**
 * Created by PhpStorm.
 * User: StephenRaharja
 * Date: 2018/06/08
 * Time: 16:47
 */

abstract class DataExtender
{
    /**
     * Method for extending a object array
     *
     * @param  array      $data       The array to be extended
     * @param string|null $path       Hash::Extract() Path to the ID
     * @param string|null $extKeyName Key name for the extended data. Insert if necessary
     *
     * @return array Extended data
     */
    public final function extend(array $data, string $path, string $extKeyName = 'id'): array
    {
        $keys = Hash::extract($data, $path);

        if (!empty($keys)) {
            $dataExtension = $this->fetchData($keys);

            //Since extract path is split with '.' , tokenize string by it and get the last element
            $tokens = explode('.', $path);
            $parentKey = end($tokens);

            return $this->connectData($data, $parentKey, $dataExtension, $extKeyName);
        }
        return $data;
    }

    /**
     * Fetch the data extension from their respective models
     *
     * @param array $keys
     *
     * @return array
     */
    abstract protected function fetchData(array $keys): array;

    /**
     * Append extended data to the parent array
     *
     * @param array  $parentData
     * @param string $parentKeyName
     * @param array  $extData
     * @param string $extDataKey
     *
     * @return array Connected original data with extension data
     */
    protected function connectData(
        array $parentData,
        string $parentKeyName,
        array $extData,
        string $extDataKey
    ): array {
        foreach ($parentData as &$parentElement) {
            foreach ($extData as $extension) {
                //Since extension data will have its own Model name as key, we use extract
                //E.g. ['User'][...]
                if (Hash::get($parentElement, $parentKeyName) ==
                    Hash::extract($extension, "{s}." . $extDataKey)[0]) {
                    $parentElement = array_merge($parentElement, array_change_key_case($extension));
                    break;
                }

            }
        }
        return $parentData;
    }

    /**
     * Remove null keys and return unique ones
     *
     * @param $array
     *
     * @return array
     */
    protected final function filterKeys($array)
    {
        return array_unique(array_filter($array, function ($value) {
            return !empty($value);
        }));
    }
}
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
     * @param  array      $data             The array to be extended
     * @param string|null $path             Hash::Extract() Path to the ID
     * @param string|null $extensionKeyName Key name for the extended data. Insert if necessary
     */
    public final function extend(array &$data, string $path, string $extensionKeyName = 'id')
    {
        $idArray = Hash::extract($data, $path);

        if (!empty($idArray)) {
            $dataExtension = $this->fetchData($idArray);
            $this->connectData($data, $path, $dataExtension, $extensionKeyName);
        }
    }

    /**
     * Fetch the data extension from their respective models
     *
     * @param array $idArray
     *
     * @return array
     */
    abstract protected function fetchData(array $idArray): array;

    /**
     * Append extended data to the parent array
     *
     * @param array  $parentData
     * @param string $parentKeyName
     * @param array  $extensionData
     * @param string $extensionKeyName
     */
    protected final function connectData(
        array &$parentData,
        string $parentKeyName,
        array $extensionData,
        string $extensionKeyName
    ) {
        foreach ($parentData as $parentElement) {
            foreach ($extensionData as $extension) {
                //Since extension data will have its own Model name as key, we use extract
                //E.g. ['User'][...]
                if (Hash::get($parentElement, $parentKeyName) ===
                    Hash::extract($extension, "{s}." . $extensionKeyName)) {
                    $parentData[] = $extension;
                    break;
                }
            }
        }
    }
}
<?php
/**
 * Created by PhpStorm.
 * User: StephenRaharja
 * Date: 2018/06/08
 * Time: 16:47
 */

abstract class DataExtension
{
    /**
     * Method for extending a object array
     *
     * @param array  $data        The array to be extended
     * @param string $parentKey
     * @param string $extKeyName
     * @param string $extEntryKey Custom array key for data extension in the resulting array. Default to model name.
     *
     * @return array Extended data
     */
    public final function extend(
        array $data,
        string $parentKey,
        string $extKeyName = 'id',
        string $extEntryKey = ""
    ): array {
        $keys = $this->getKeys($data, $parentKey);
        if (!empty($keys)) {
            $dataExtension = $this->fetchData($keys);
            $tmp = $this->connectData([$data], $parentKey, $dataExtension, $extKeyName, $extEntryKey);
            $data = reset($tmp);
        }
        return $data;
    }

    /**
     * Method for extending a object array
     *
     * @param array       $data        The array to be extended
     * @param string|null $path        Hash::Extract() Path to the ID
     * @param string      $extKeyName  Key name for the extended data. Insert if necessary
     * @param string      $extEntryKey Custom array key for data extension in the resulting array. Default to model name.
     *
     * @return array Extended data
     */
    public final function extendMulti(
        array $data,
        string $path,
        string $extKeyName = 'id',
        string $extEntryKey = ""
    ): array {
        $keys = $this->getKeys($data, $path);

        if (!empty($keys)) {

            $dataExtension = $this->fetchData($keys);

            //Remove header {n} from path
            $separatorIndex = strpos($path, '.');
            if ($separatorIndex > 0 && $separatorIndex + 1 < strlen($path)) {
                $parentKey = substr($path, $separatorIndex + 1);
            } else {
                //Since extract path is split with '.' , tokenize string by it and get the last element
                $tokens = explode('.', $path);
                $parentKey = end($tokens);
            }

            return $this->connectData($data, $parentKey, $dataExtension, $extKeyName, $extEntryKey);
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
     * @param string $extEntryKey Custom array key for data extension in the resulting array. Default to model name.
     *
     * @return array Connected original data with extension data
     */
    protected function connectData(
        array $parentData,
        string $parentKeyName,
        array $extData,
        string $extDataKey,
        string $extEntryKey = ""
    ): array {
        foreach ($parentData as $key => &$parentElement) {
            foreach ($extData as $extension) {
                //If parent data is a single model without int as index
                if (!is_int($key)) {
                    //Since extension data will have its own Model name as key, we use extract
                    //E.g. ['User'][...]
                    if (Hash::get($parentData, $parentKeyName) ==
                        Hash::extract($extension, "{s}." . $extDataKey)[0]) {
                        if (empty($extEntryKey)) {
                            $parentData = array_merge($parentData, AppUtil::arrayChangeKeySnakeCase($extension));
                        } else {
                            $parentData[$extEntryKey] = Hash::extract($extension, '{s}')[0];
                        }
                        break;
                    }
                    return $parentData;
                }

                //Since extension data will have its own Model name as key, we use extract
                //E.g. ['User'][...]
                if (Hash::get($parentElement, $parentKeyName) ==
                    Hash::extract($extension, "{s}." . $extDataKey)[0]) {
                    if (empty($extEntryKey)) {
                        $parentElement = array_merge($parentElement, AppUtil::arrayChangeKeySnakeCase($extension));
                    } else {
                        $parentElement[$extEntryKey] = Hash::extract($extension, '{s}')[0];
                    }
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

    private function getKeys(array $data, string $path): array
    {
        if (empty($data)) {
            return [];
        }

        if (is_int(array_keys($data)[0])) {
            return Hash::extract($data, $path);
        } else {
            $parentKey = ltrim($path, '{n}.');

            return [Hash::get($data, $parentKey)];
        }
    }
}

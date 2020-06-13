<?php
App::uses("PostShareCircle", "Model");
App::import('Lib/DataExtender/Extension', 'DataExtension');

class CircleIdExtension
{
    /**
     * Method for extending a object array
     *
     * @param  array $data        The array to be extended
     * @param string $parentKey
     * @param string $extKeyName
     * @param string $extEntryKey Custom array key for data extension in the resulting array. Default to model name.
     *
     * @return array Extended data
     */
    public function extend(
        array $data,
        string $parentKey,
        string $extKeyName = 'id',
        string $extEntryKey = ""
    ): array {
        if (empty($data['circle_id'])) {
            /** @var PostShareCircle $PostShareCircle */
            $PostShareCircle = ClassRegistry::init('PostShareCircle');
            $postId = $data['id'];
            $data['circle_id'] = $PostShareCircle->getFirstSharedCircleId($postId);
        }
        return $data;
    }

    /**
     * Method for extending a object array
     *
     * @param  array      $data        The array to be extended
     * @param string|null $path        Hash::Extract() Path to the ID
     * @param string      $extKeyName  Key name for the extended data. Insert if necessary
     * @param string      $extEntryKey Custom array key for data extension in the resulting array. Default to model name.
     *
     * @return array Extended data
     */
    public function extendMulti(
        array $data,
        string $path,
        string $extKeyName = 'id',
        string $extEntryKey = ""
    ): array {
        return array_map(function ($postData) {
           return $this->extend($postData, '');
        }, $data);
    }
}

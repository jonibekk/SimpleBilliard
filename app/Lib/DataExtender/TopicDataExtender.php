<?php
App::uses("Topic", "Model");
App::import('Lib/DataExtender', 'BaseExtender');
/**
 * Created by PhpStorm.
 * User: Stephen Raharja
 * Date: 12/17/2018
 * Time: 2:37 PM
 */

class TopicDataExtender extends BaseExtender
{
    const EXTEND_ALL = "ext:topic:all";
    const EXTEND_CREATOR = "ext:topic:creator";

    public function extend(array $data, int $userId, int $teamId, array $extensions = []): array
    {
        throw new RuntimeException("Please implement " . __METHOD__);
    }

    public function extendMulti(array $data, int $userId, int $teamId, array $extensions = []): array
    {
        if ($this->includeExt($extensions, self::EXTEND_CREATOR)) {
            /** @var UserExtension $UserExtension */
            $UserExtension = ClassRegistry::init('UserExtension');
            $data = $UserExtension->extendMulti($data, "{n}.creator_user_id");
        }

        return $data;
    }

}
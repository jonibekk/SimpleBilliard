<?php
App::import('Lib/DataExtender', 'BaseExtender');
App::import('Lib/DataExtender/Extension', 'CircleMemberInfoExtension');
App::import('Lib/DataExtender/Extension', 'CircleMemberCountExtension');
App::import('Service', 'ImageStorageService');

class CircleExtender extends BaseExtender
{
    const EXTEND_ALL = 'ext:circle:all';
    const EXTEND_MEMBER_INFO = 'ext:circle:member_info';
    const EXTEND_MEMBER_COUNT = 'ext:circle:member_count';

    public function extend(array $data, int $userId, int $teamId, array $extensions = []): array
    {
        // TODO:implement
    }

    public function extendMulti(array $data, int $userId, int $teamId, array $extensions = []): array
    {
        // Set image url each circle
        /** @var ImageStorageService $ImageStorageService */
        $ImageStorageService = ClassRegistry::init('ImageStorageService');
        foreach ($data as $i => $v) {
            $data[$i]['img_url'] = $ImageStorageService->getImgUrlEachSize($data[$i], 'Circle');
        }

        if ($this->includeExt($extensions, self::EXTEND_MEMBER_INFO)) {

            /** @var CircleMemberInfoExtension $CircleMemberInfoExtension */
            $CircleMemberInfoExtension = ClassRegistry::init('CircleMemberInfoExtension');

            $CircleMemberInfoExtension->setUserId($userId);
            $data = $CircleMemberInfoExtension->extendMulti($data, "{n}.id", "circle_id");

        }

        // Originally circles table has `circle_member_count` column. but this column hasn't been maintained. So we shouldn't use this column and overwrite key value.
        if ($this->includeExt($extensions, self::EXTEND_MEMBER_COUNT)) {
            /** @var CircleMemberCountExtension $CircleMemberCountExtension */
            $CircleMemberCountExtension = ClassRegistry::init('CircleMemberCountExtension');
            $data = $CircleMemberCountExtension->extendMulti($data, "{n}.id");
        }
        return $data;
    }
}

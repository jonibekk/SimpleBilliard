<?php
App::import('Lib/DataExtender', 'BaseExtender');
App::import('Service', 'ImageStorageService');
/**
 * Created by PhpStorm.
 * User: stephen
 * Date: 19/04/10
 * Time: 17:47
 */

class TeamExtender extends BaseExtender
{
    const EXTEND_ALL = "ext:team:all";

    public function extend(array $data, int $userId, int $teamId, array $extensions = []): array
    {
        // Set image url each circle
        /** @var ImageStorageService $ImageStorageService */
        $ImageStorageService = ClassRegistry::init('ImageStorageService');
        $data['img_url'] = $ImageStorageService->getImgUrlEachSize($data, 'Team');

        return $data;
    }

    public function extendMulti(array $data, int $userId, int $teamId, array $extensions = []): array
    {
        // Set image url each circle
        /** @var ImageStorageService $ImageStorageService */
        $ImageStorageService = ClassRegistry::init('ImageStorageService');
        foreach ($data as $i => $v) {
            $data[$i]['img_url'] = $ImageStorageService->getImgUrlEachSize($data[$i], 'Team');
        }

        return $data;
    }
}
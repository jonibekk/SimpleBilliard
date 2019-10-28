<?php
App::import('Lib/DataExtender', 'BaseExtender');
App::import('Service', 'ImageStorageService');
App::import('Service', 'VideoStreamService');

class TeamExtender extends BaseExtender
{
    const EXTEND_ALL = "ext:team:all";

    public function extend(array $data, int $userId, int $teamId, array $extensions = []): array
    {
        // Set image url each circle
        /** @var ImageStorageService $ImageStorageService */
        $ImageStorageService = ClassRegistry::init('ImageStorageService');
        $data['img_url'] = $ImageStorageService->getImgUrlEachSize($data, 'Team');

        $data['configs'] = [];
        /** @var VideoStreamService $VideoStreamService */
        $VideoStreamService = ClassRegistry::init('VideoStreamService');
        $data['configs']['video_duration_max_seconds'] = $VideoStreamService->getTeamVideoDurationLimit($teamId);
        $data['configs']['file_size_mb_max_upload_video'] = $VideoStreamService->getTeamVideoFileSizeMBMax($teamId);

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

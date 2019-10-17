<?php
App::uses('AppModel', 'Model');
App::import('Model/Entity', 'TeamTranslationUsageLogEntity');

use Goalous\Enum\DataType\DataType as DataType;

class TeamConfig extends AppModel
{
    public $modelConversionTable = [
        'config' => DataType::JSON
    ];

    public function getConfig(int $teamId): TeamConfigValues
    {
        $teamConfig = $this->find('first', [
            'conditions' => [
                'team_id' => $teamId
            ]
        ]);
        if (empty($teamConfig)) {
            return new TeamConfigValues();
        }
        return TeamConfigValues::createFromJsonString($teamConfig['TeamConfig']['config']);
    }

    public function updateConfig(int $teamId, TeamConfigValues $teamConfigValues)
    {
        // FYI: Avoid using CakePHP updateAll()
        // This method handling json string, need a careful quote and sanitizing.
        // https://qiita.com/HamaTech/items/cc2b3b2a44f7aba0b71e

        $teamConfig = $this->find('first', [
            'conditions' => [
                'team_id' => $teamId
            ]
        ]);
        $configString = json_encode($teamConfigValues);
        if (empty($teamConfig)) {
            $this->create();
            $this->save([
                'team_id' => $teamId,
                'config' => $configString,
            ]);
        } else {
            $teamConfig['TeamConfig']['config'] = $configString;
            $this->save($teamConfig);
        }
    }
}

class TeamConfigValues implements JsonSerializable
{
    const KEY_VIDEO_DURATION_MAX_SECONDS = 'video_duration_max_seconds';
    const KEY_FILE_SIZE_MB_MAX_VIDEO = 'file_size_mb_max_video';

    /**
     * The duration second to cut off video
     * @var int|null
     */
    protected $videoDurationMaxSecond;

    /**
     * Max video file size of upload-able (MB)
     * @var int|null
     */
    protected $fileSizeMbMaxVideo;

    /**
     * @return int|null
     */
    public function getVideoDurationMaxSecond()
    {
        return $this->videoDurationMaxSecond;
    }

    /**
     * @param int|null $videoDurationMaxSecond
     */
    public function setVideoDurationMaxSecond($videoDurationMaxSecond)
    {
        $this->videoDurationMaxSecond = $videoDurationMaxSecond;
    }

    /**
     * @return int|null
     */
    public function getFileSizeMbMaxVideo(): ?int
    {
        return $this->fileSizeMbMaxVideo;
    }

    /**
     * @param int|null $fileSizeMbMaxVideo
     */
    public function setFileSizeMbMaxVideo(int $fileSizeMbMaxVideo): void
    {
        $this->fileSizeMbMaxVideo = $fileSizeMbMaxVideo;
    }

    public static function createFromJsonString(string $jsonString): self
    {
        return static::createFromArray(json_decode($jsonString, true));
    }

    public static function createFromArray(array $teamConfigArray): self
    {
        $instance = new self();
        if (!empty($teamConfigArray[static::KEY_VIDEO_DURATION_MAX_SECONDS])) {
            $instance->setVideoDurationMaxSecond($teamConfigArray[static::KEY_VIDEO_DURATION_MAX_SECONDS]);
        }
        if (!empty($teamConfigArray[static::KEY_FILE_SIZE_MB_MAX_VIDEO])) {
            $instance->setFileSizeMbMaxVideo($teamConfigArray[static::KEY_FILE_SIZE_MB_MAX_VIDEO]);
        }
        return $instance;
    }

    public function toArray()
    {
        $r = [];
        if (!empty($this->videoDurationMaxSecond)) {
            $r[static::KEY_VIDEO_DURATION_MAX_SECONDS] = $this->getVideoDurationMaxSecond();
        }
        if (!empty($this->fileSizeMbMaxVideo)) {
            $r[static::KEY_FILE_SIZE_MB_MAX_VIDEO] = $this->getFileSizeMbMaxVideo();
        }
        return $r;
    }

    public function jsonSerialize()
    {
        return $this->toArray();
    }
}

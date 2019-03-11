<?php
App::uses("Comment", "Model");
App::uses("CommentRead", "Model");
App::uses('MentionComponent', 'Controller/Component');
App::import('Lib/DataExtender/Extension', 'DataExtension');

class MentionsToMeExtension extends DataExtension
{
    /** @var int */
    private $userId;
    /** @var int */
    private $teamId;
    /** @var array */
    private $mapIdAndData;

    /**
     * Set user ID for the extender function
     *
     * @param int $userId
     */
    public function setUserId(int $userId)
    {
        $this->userId = $userId;
    }

    /**
     * Set team ID for the extender function
     *
     * @param int $teamId
     */
    public function setTeamId(int $teamId)
    {
        $this->teamId = $teamId;
    }

    /**
     * Set mapping id and data for the extender function
     *
     * @param array $mapIdAndData
     */
    public function setMap(array $mapIdAndData)
    {
        $this->mapIdAndData = $mapIdAndData;
    }

    protected function fetchData(array $keys): array
    {
        if (empty($this->userId)) {
            throw new RuntimeException("Missing user ID");
        }

        $commentIds = $this->filterKeys($keys);
        /** @var MentionComponent $Mention */
        $Mention = new MentionComponent(new ComponentCollection());

        $res = [];
        foreach($commentIds as $commentId) {
            $userId = Hash::get($this->mapIdAndData, $commentId.".user_id");
            if ($userId == $this->userId) {
                $res[$commentId] = [];
                continue;
            }
            $body = Hash::get($this->mapIdAndData, $commentId.".body");
            $res[$commentId] = $Mention->getMyMentions($body, $this->userId, $this->teamId);
        }


        return $res;
    }

    protected function connectData(
        array $parentData,
        string $parentKeyName,
        array $extData,
        string $extDataKey,
        string $extEntryKey = ""
    ): array
    {
        foreach ($parentData as $key => &$parentElement) {
            $key = Hash::get($parentElement, $parentKeyName);
            $parentElement['mentions_to_me'] = Hash::get($extData, $key);
        }
        return $parentData;
    }

}

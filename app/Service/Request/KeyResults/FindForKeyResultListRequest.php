<?php
App::uses('TeamEntity', 'Model/Entity');
App::uses('TermEntity', 'Model/Entity');
App::uses('AppUtil', 'Util');

class FindForKeyResultListRequest
{
    /**
     * @var integer
     */
    protected $userId;

    /**
     * @var TermEntity
     */
    protected $term;

    /**
     * @var TeamEntity
     */
    protected $team;

    /**
     * @var string
     */
    protected $todayDate;

    /**
     * @var null|boolean
     */
    protected $onlyIncomplete;

    /**
     * @var null|integer
     */
    protected $limit;

    public function __construct(int $userId, TeamEntity $team, TermEntity $term, array $opts)
    {
        $this->userId = $userId;
        $this->team = $team;
        $this->term = $term;
        $this->todayDate = AppUtil::dateYmdLocal(time(), $team['timezone']);

        if (array_key_exists('onlyIncomplete', $opts)) {
            $this->onlyIncomplete = $opts['onlyIncomplete'];
        }

        if (array_key_exists('limit', $opts)) {
            $this->limit = $opts['limit'];
        }
    }

    public function getUserId()
    {
        return $this->userId;
    }

    public function getTeam()
    {
        return $this->team;
    }

    public function getTerm()
    {
        return $this->term;
    }

    public function getOnlyIncomplete()
    {
        return $this->onlyIncomplete;
    }

    public function getLimit()
    {
        return $this->limit;
    }

    public function getTodayDate()
    {
        return $this->todayDate;
    }
}

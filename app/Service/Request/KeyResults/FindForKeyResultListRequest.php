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
     * @var integer
     */
    protected $teamId;

    /**
     * @var TermEntity
     */
    protected $term;

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

    public function __construct(int $userId, int $teamId, TermEntity $term, array $opts)
    {
        $this->userId = $userId;
        $this->teamId = $teamId;
        $this->term = $term;
        $this->todayDate = GoalousDateTime::now()->format('Y-m-d');

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

    public function getTeamId()
    {
        return $this->teamId;
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

    public function isPastTerm()
    {
        return $this->todayDate > $this->term['end_date'];
    }
}

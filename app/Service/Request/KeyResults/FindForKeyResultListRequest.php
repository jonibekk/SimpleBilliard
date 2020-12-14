<?php
App::uses('TeamEntity', 'Model/Entity');
App::uses('TermEntity', 'Model/Entity');
App::import('Service', 'TermService');
App::uses('Team', 'Model');
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
     * @var integer
     */
    protected $goalId;

    /**
     * @var integer
     */
    protected $listId;

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

    public function __construct(int $userId, int $teamId, array $opts)
    {

        $this->userId = $userId;
        $this->teamId = $teamId;
        $this->todayDate = GoalousDateTime::now()->format('Y-m-d');
        $this->initializeTerm($opts);

        if (array_key_exists('onlyIncomplete', $opts)) {
            $this->onlyIncomplete = $opts['onlyIncomplete'];
        }

        if (array_key_exists('limit', $opts)) {
            $this->limit = $opts['limit'];
        }

        if (array_key_exists('listId', $opts)) {
            $this->listId = $opts['listId'];
        }

        if (array_key_exists('goalId', $opts)) {
            $this->goalId = $opts['goalId'];
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

    public function getListId()
    {
        return $this->listId;
    }

    public function getGoalId()
    {
        return $this->goalId;
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
        return strtotime($this->todayDate) > strtotime($this->term['end_date']);
    }

    private function initializeTerm(array $opts) 
    {
        // @var TermService ;
        $TermService = ClassRegistry::init("TermService");
        // @var Term ;
        $Term = ClassRegistry::init("Term");

        if (array_key_exists('termId', $opts)) {
            $termId = $opts['termId'];

            if (!empty($termId) && $termId !== 'current') {
                $this->term = $Term->useType()->useEntity()->findById($termId);
                return;
            }
        } 

        $this->term = $TermService->getCurrentTerm($this->teamId);
    }

    public function log() {
        GoalousLog::info('request', get_object_vars($this));
    }
}

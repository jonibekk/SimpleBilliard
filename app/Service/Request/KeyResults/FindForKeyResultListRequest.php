<?php
App::uses('TeamEntity', 'Model/Entity');
App::uses('TermEntity', 'Model/Entity');
App::uses('Team', 'Model');
App::uses('AppUtil', 'Util');
App::import('Service', 'GoalService');
App::import('Service', 'TermService');

class FindForKeyResultListRequest
{
    const TERM_ID_RECENT = 'recent';
    const TERM_ID_CURRENT = 'current';
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
     * @var string
     */
    protected $periodFrom;

    /**
     * @var string
     */
    protected $periodTo;

    /**
     * @var boolean
     */
    protected $onlyRecent;

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
        $this->term = $this->initializeTerm($opts);

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
            if ($opts['goalId'] !== null) {
                $this->goalId = intval($opts['goalId']);
            }
        }

        if (array_key_exists('termId', $opts)) {
            $this->onlyRecent = $opts['termId'] === self::TERM_ID_RECENT;
        }

        $this->initializePeriod();
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

    public function getOnlyRecent()
    {
        return $this->onlyRecent;
    }

    public function getPeriodFrom()
    {
        return $this->periodFrom;
    }

    public function getPeriodTo()
    {
        return $this->periodTo;
    }

    private function initializeTerm(array $opts) 
    {
        // @var TermService ;
        $TermService = ClassRegistry::init("TermService");
        // @var Term ;
        $Term = ClassRegistry::init("Term");

        if (array_key_exists('termId', $opts)) {
            $termId = $opts['termId'];
            $isNotCurrentTerm = !in_array($termId, [self::TERM_ID_CURRENT, self::TERM_ID_RECENT]);

            if (!empty($termId) && $isNotCurrentTerm) {
                return $Term->useType()->useEntity()->findById($termId);
            }
        } 

        return $TermService->getCurrentTerm($this->teamId);
    }

    private function initializePeriod()
    {
        if ($this->getOnlyRecent()) {
            /** @var GoalService $GoalService */
            $GoalService = ClassRegistry::init('GoalService');

            $results =  $GoalService->getGraphRange(
                $this->todayDate,
                GoalService::GRAPH_TARGET_DAYS,
                GoalService::GRAPH_MAX_BUFFER_DAYS
            );
            $this->periodFrom = $results['graphStartDate'];
            $this->periodTo = $results['graphEndDate'];
            
        } else {
            $this->periodFrom = $this->term['start_date'];
            $this->periodTo = $this->term['end_date'];
        }
    }

    public function log() {
        CustomLogger::getInstance('request', get_object_vars($this));
    }
}

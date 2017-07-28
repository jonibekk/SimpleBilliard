<?php
App::import('Service', 'AppService');
App::uses('Team', 'Model');

/**
 * Class TeamService
 */
class TeamService extends AppService
{

    function add(array $data, int $userId)
    {
        /** @var Team $Team */
        $Team = ClassRegistry::init("Team");
        /** @var Term $Term */
        $Term = ClassRegistry::init('Term');

        try {
            $Team->begin();

            if (!$Team->add($data, $userId)) {
                throw new Exception(sprintf("Failed to create team. data: %s userId: %s",
                    var_export($data, true), $userId));
            }
            $teamId = $Team->getLastInsertID();

            // save current & next & next next term
            $nextStartDate = date('Y-m-01', strtotime($data['Team']['next_start_ym']));
            $termRange = $data['Team']['border_months'];
            $currentStartDate = date('Y-m-01');
            if (!$Term->createInitialDataAsSignup($currentStartDate, $nextStartDate, $termRange, $teamId)) {
                throw new Exception(sprintf("Failed to create term. data: %s teamId: %s userId: %s",
                    var_export($data, true), $teamId, $userId));
            }

            $Team->commit();
        } catch (Exception $e) {
            $this->log(sprintf("[%s]%s", __METHOD__, $e->getMessage()));
            $this->log($e->getTraceAsString());
            $Team->rollback();
            return false;
        }

        return true;
    }

    /**
     * get team service use status
     * # Warning
     * - In Team::getCurrentTeam, use CACHE_KEY_CURRENT_TEAM cache.
     * - So when change service use status, must delete this team cache.
     *
     * @return int
     */
    public function getServiceUseStatus(): int
    {
        /** @var Team $Team */
        $Team = ClassRegistry::init("Team");

        $team = $Team->getCurrentTeam();
        return $team['Team']['service_use_status'];
    }

    /**
     * check read onlhy or not
     *
     * @return bool
     */
    public function isReadOnly(): bool
    {
        return $this->getServiceUseStatus() == Team::SERVICE_USE_STATUS_READ_ONLY;
    }

    public function isCannotUseService(): bool
    {
        return $this->getServiceUseStatus() == Team::SERVICE_USE_STATUS_CANNOT_USE;
    }
}

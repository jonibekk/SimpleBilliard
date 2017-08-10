<?php
App::uses('AppUtil', 'Util');

class TeamServiceUseStatusStartEndDateUpdateShell extends AppShell
{

    var $uses = array(
        'Team'
    );

    public function startup()
    {
        parent::startup();
    }

    /**
     * @return ConsoleOptionParser
     */
    public function getOptionParser(): ConsoleOptionParser
    {
        $parser = parent::getOptionParser();
        $options = [
            'timestamp' => [
                'short'    => 't',
                'help'     => '[ Saving date as team service use start date ]',
                'required' => false,
            ],
        ];
        $parser->addOptions($options);
        return $parser;
    }

    /**
     * main process
     * - update existing all team `service_use_state_start_date`
     *   - should set time as start of team free trial
     *
     * @return void
     */
    public function main()
    {
        $currentTime = $this->params['timestamp'] ?? time();
        $currentDate = date('Y-m-d', $currentTime);

        try {
            $this->Team->begin();

            if(!$this->Team->updateAllServiceUseStateStartEndDate(Team::SERVICE_USE_STATUS_FREE_TRIAL, $currentDate)) {
                throw new Exception(sprintf("Failed to update service use state start date"));
            }

        } catch (Exception $e) {
            // transaction rollback
            $this->Team->rollback();
            CakeLog::error($e->getMessage());
            CakeLog::error($e->getTraceAsString());
            exit(1);
        }

        $this->Team->commit();
    }
}

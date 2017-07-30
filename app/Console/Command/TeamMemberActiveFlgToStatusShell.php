<?php

class TeamMemberActiveFlgToStatusShell extends AppShell
{

    var $uses = array(
        'TeamMember'
    );

    public function startup()
    {
        parent::startup();
    }

    public function main()
    {
        try {
            $this->TeamMember->begin();

            if(!$this->TeamMember->updateActiveFlgToStatus()) {
                throw new Exception(sprintf("Failed to update active flg to new status. "));
            }


            if (!$this->TeamMember->updateInactiveFlgToStatus(false)) {
                throw new Exception(sprintf("Failed to update inactive flg to new status. "));
            }

        } catch (Exception $e) {
            // transaction rollback
            $this->TeamMember->rollback();
            CakeLog::error($e->getMessage());
            CakeLog::error($e->getTraceAsString());
            // if return false, it will be paused to wait input.. So, exit
            exit(1);
        }

        $this->TeamMember->commit();
    }
}

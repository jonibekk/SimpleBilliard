<?php
/**
 * DetectInconsistentDataShell
 * Detect inconsistent data of parent shell
 *
 * @property Goal $Goal
 */
class DetectInconsistentDataShell extends AppShell
{
    public function startup()
    {
        parent::startup();
    }

    public function main()
    {
        /* Detect inconsistent data each type */
        // term
        $this->dispatchShell('DetectInconsistentTerm');
        // goal
        $this->dispatchShell('DetectInconsistentGoal');
        // kr
        $this->dispatchShell('DetectInconsistentKr');
    }
}

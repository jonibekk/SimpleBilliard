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
        $this->dispatchShell('DetectInconsistent.DetectInconsistentTerm');
        // goal
        $this->dispatchShell('DetectInconsistent.DetectInconsistentGoal');
        // kr
        $this->dispatchShell('DetectInconsistent.DetectInconsistentKr');
    }
}

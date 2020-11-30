<?php
App::uses('AppUtil', 'Util');

/**
 * ActionMembersMigrationShell
 *
 * @property ActionResult $ActionResult
 * @property ActionResultMember $ActionResultMember
 */
class ActionMembersMigrationShell extends AppShell
{
    public function startup()
    {
        parent::startup();
    }

    public function main()
    {
        CakeLog::Info("Start: get all action_result_member id.");
        $allActionResultMemberIdList = $this->getAllActionResultMemberId();
        CakeLog::Info("Finish: get all action_result_member id.");
        CakeLog::Info("Number of target: " . count($allActionResultMemberIdList));


        CakeLog::Info("Start: get action_result id without already registerd.");
        $ActionResutsList = $this->getActionResultIdWithoutAlreadyRegistered($allActionResultMemberIdList);
        CakeLog::Info("Finish: get action_result id without already registerd.");
        CakeLog::Info("Number of target: " . count($ActionResutsList));


        CakeLog::Info("Start: add action_result_member record.");
        $resultInsert = $this->addActionResultMembers($ActionResutsList);
        CakeLog::Info("Finish: add action_result_member record.");

        if($resultInsert === true) {
            CakeLog::Info("Migration Successed!");
        }
        else {
            CakeLog::Error("Migration Failed!");
            foreach($resultInsert as $fail) {
                CakeLog::Error($fail);
            }
        }
    }

    function getAllActionResultMemberId() {
        $ActionResultMember = ClassRegistry::init('ActionResultMember');

        try {
            return $ActionResultMember->find('list', [
                'conditions' => [
                    'ActionResultMember.del_flg' => false,
                    'ActionResultMember.is_action_creator' => true
                ],
                'fields' => [
                    'ActionResultMember.action_result_id'
                ]
            ]);
        }
        catch (Exception $e) {
            CakeLog::Error($e);
        }
    }

    function getActionResultIdWithoutAlreadyRegistered($actionResultMemberIdList) {
        $ActionResult = ClassRegistry::init('ActionResult');

        try {
            return $ActionResult->find('all', [
                'conditions' => [
                    'ActionResult.del_flg' => false,
                    'NOT' => [
                        'ActionResult.id' => $actionResultMemberIdList
                    ]
                ],
                'fields' => [
                    'ActionResult.id',
                    'ActionResult.team_id',
                    'ActionResult.user_id'
                ],
            ]);
        }
        catch (Exception $e) {
            CakeLog::Error($e);
        }
    }

    function addActionResultMembers($ActionResutsList) {
        $ActionResultMember = ClassRegistry::init('ActionResultMember');

        $successCnt = 0;
        $failList = [];
        foreach ($ActionResutsList as $row) {
            try {
                $result = $ActionResultMember->addMember($row["ActionResult"]['id'],
                                                         $row["ActionResult"]['user_id'],
                                                         $row["ActionResult"]['team_id'],
                                                         true);
                if($result) {
                    // success
                    $successCnt++;
                    if ($successCnt % 1000 === 0) {
                        CakeLog::Info(sprintf('success count: %d', $successCnt));
                    }
                }
            }
            catch(Exception $e) {
                // fail
                $failList[] = $row["ActionResult"]['id'];

                CakeLog::Error($e);
            }
        }

        CakeLog::Info("Success: " . $successCnt);
        CakeLog::Info("Fail   : " . count($failList));

        if(count($failList) == 0) {
            return true;
        }
        else {
            return $failList;
        }
    }
}

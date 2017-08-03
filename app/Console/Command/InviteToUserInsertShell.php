<?php
App::uses('AppUtil', 'Util');

class InviteToUserInsertShell extends AppShell
{
    var $uses = array(
        'Invite',
        'User',
        'Email',
        'TeamMember'
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
            'currentTimestamp' => [
                'short'    => 'c',
                'help'     => '[ It is used for only test cases ]',
                'required' => false,
            ],
        ];
        $parser->addOptions($options);
        return $parser;
    }

    public function main()
    {
        $currentTimestamp = $this->params['currentTimestamp'] ?? time();
        $targetInvites = $this->Invite->findUnverifiedBeforeExpired($currentTimestamp);
        $this->log($targetInvites);
        if (count($targetInvites) === 0) {
            return;
        }
        return;

        try {
            $this->User->begin();

            $newUserInvites = Hash::combine($targetInvites, '{n}.Invite[!to_user_id]', '{n}');
            // register new user
            $insertEmails = [];
            foreach ($newUserInvites as $invite) {
                $teamId = $invite['Invite']['team_id'];
                $email = $invite['Invite']['email'];
                $this->User->create();
                if (!$this->User->save(['team_id' => $teamId], false)) {
                    throw new Exception(sprintf("Failed to insert users. data:%s",
                            AppUtil::varExportOneLine(compact('emails', 'newEmails', 'teamId', 'fromUserId')))
                    );
                }
                $insertEmails[] = [
                    'user_id' => $this->User->getLastInsertID(),
                    'email'   => $email
                ];
            }
            /* Insert emails table */
            if (!$Email->bulkInsert($insertEmails)) {
                throw new Exception(sprintf("Failed to insert emails. data:%s",
                        AppUtil::varExportOneLine(compact('emails', 'insertEmails', 'teamId')))
                );
            }

            /* Insert team_members table */
            // Except for already belonged to target team
            $targetUserIds = $this->User->findNotBelongToTeamByEmail($emails);
            $insertTeamMembers = [];
            foreach ($targetUserIds as $userId) {
                $insertTeamMembers[] = [
                    'user_id' => $userId,
                    'team_id' => $teamId,
                    'status'  => TeamMember::STATUS_INVITED
                ];
            }
            if (!$TeamMember->bulkInsert($insertTeamMembers)) {
                throw new Exception(sprintf("Failed to insert team members. data:%s",
                        AppUtil::varExportOneLine(compact('insertTeamMembers', 'targetInvites')))
                );
            }

        } catch (Exception $e) {
            // transaction rollback
            $this->User->rollback();
            CakeLog::error($e->getMessage());
            CakeLog::error($e->getTraceAsString());
            exit(1);
        }
        $this->User->commit();
    }
}

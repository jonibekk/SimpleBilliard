<?php
App::import('Service', 'AppService');
App::uses('TeamMember', 'Model');

/**
 * Class TeamMemberService
 */
class TeamMemberService extends AppService
{
    /**
     * Activate team member
     *
     * @param int $teamId
     *
     * @return bool
     */
    public function activate(int $teamId, int $teamMemberId): bool
    {
        /** @var TeamMember $TeamMember */
        $TeamMember = ClassRegistry::init("TeamMember");
        /** @var Team $Team */
        $Team = ClassRegistry::init("Team");
        /** @var PaymentService $PaymentService */
        $PaymentService = ClassRegistry::init('PaymentService');

        try {
            $this->TransactionManager->begin();

            // Team member activate
            if (!$TeamMember->activate($teamMemberId)) {
                throw new Exception(sprintf("Failed to activate team member. data:%s",
                AppUtil::varExportOneLine(compact('teamId', 'teamMemberId'))));
            }

            // Charge if paid plan
            if ($Team->isPaidPlan($teamId)) {
                $PaymentService->charge(
                    $teamId, Enum\ChargeHistory\ChargeType::USER_ACTIVATION_FEE()
                );
            }
        } catch (Exception $e) {
            $this->TransactionManager->rollback();
            $this->log(sprintf("[%s]%s", __METHOD__, $e->getMessage()));
            $this->log($e->getTraceAsString());
            return false;
        }

        $this->TransactionManager->commit();
        return true;
    }
}
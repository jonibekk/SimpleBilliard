<?php
App::uses('AppModel', 'Model');
App::uses('PaymentUtil', 'Util');
App::uses('AppUtil', 'Util');

/**
 * Class PricePlanPurchaseTeam
 * Teams that purchased the campaigns plan
 * 料金プランを購入したチーム
 */
class PricePlanPurchaseTeam extends AppModel
{
    public $useTable = 'price_plan_purchase_teams';

    /**
     * Validation rules
     *
     * @var array
     */
    public $validate = [
        'price_plan_id' => [
            'numeric'                 => [
                'rule' => ['numeric'],
            ],
            'notBlank'                => [
                'required' => 'create',
                'rule'     => 'notBlank',
            ],
            'customValidateExistPlan' => [
                'rule' => 'customValidateExistPlan',
            ],
        ],
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public $validateUpdate = [
        'price_plan_code' => [
            'notBlank'                => [
                'required' => true,
                'rule'     => 'notBlank',
            ],
            'customValidateCodeFormat'                => [
                'rule'     => 'customValidateCodeFormat',
            ],
        ],
    ];

    /**
     * Check if exist price plan
     *
     * @param array $val
     *
     * @return bool
     */
    function customValidateExistPlan(array $val): bool
    {
        $pricePlanId = array_shift($val);
        if (empty($pricePlanId)) {
            return false;
        }
        /** @var CampaignPricePlan $CampaignPricePlan */
        $CampaignPricePlan = ClassRegistry::init('CampaignPricePlan');
        $pricePlan = $CampaignPricePlan->getById($pricePlanId);
        return !empty($pricePlan);
    }

    /**
     * Check if exist price plan
     *
     * @param array $val
     *
     * @return bool
     */
    function customValidateCodeFormat(array $val): bool
    {
        $pricePlanCode = array_shift($val);
        if (empty($pricePlanCode)) {
            return false;
        }
        $pricePlan = PaymentUtil::parsePlanCode($pricePlanCode);
        if (count($pricePlan) != 2) {
            return false;
        }
        if (!AppUtil::isInt($pricePlan['group_id']) || !AppUtil::isInt($pricePlan['detail_no'])) {
            return false;
        }
        return true;
    }

    /**
     * Returns true if the team has purchased a campaign plan
     *
     * @param int $teamId
     *
     * @return bool
     */
    function purchased(int $teamId): bool
    {
        $purchasedPlan = $this->getByTeamId($teamId, ['id']);
        if (!empty($purchasedPlan)) {
            return true;
        }
        return false;
    }
}


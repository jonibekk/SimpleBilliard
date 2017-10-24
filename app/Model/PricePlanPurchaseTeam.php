<?php
App::uses('AppModel', 'Model');

use Goalous\Model\Enum as Enum;

/**
 * PricePlanPurchaseTeam Model
 */
class PricePlanPurchaseTeam extends AppModel
{

    /**
     * Validation rules
     *
     * @var array
     */
    public $validate = [
      'price_plan_id' => [
          'numeric'       => ['rule' => ['numeric'],],
          'notBlank'      => [
              'required' => 'create',
              'rule'     => 'notBlank',
          ],
      ]
    ];
}

<?php
App::uses('AppModel', 'Model');
/**
 * ChargeHistory Model
 *
 */
class ChargeHistory extends AppModel {

    /**
     * Get latest max charge users
     *
     * @return int
     */
    function getLatestMaxChargeUsers(): int
    {
        $res = $this->find('first',[
                'fields'     => ['max_charge_users'],
                'conditions' => [
                    'team_id' => $this->current_team_id,
                ],
                'order'      => ['id' => 'DESC'],
            ]
        );
        return (int)Hash::get($res, 'ChargeHistory.max_charge_users');
    }
}

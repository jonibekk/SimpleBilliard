<?php
App::uses('AppModel', 'Model');

/**
 * TermsOfService Model
 */
class TermsOfService extends AppModel
{

    /**
     * Get current terms of service
     * 
     * @return array
     */
    function getCurrent(): array
    {
        $res = $this->find('first', [
            'conditions' => [
                'start_date <=' => date('Y-m-d')
            ],
            'order' => 'id DESC'
        ]);

        return Hash::get($res, 'TermsOfService') ?? [];
    }
}

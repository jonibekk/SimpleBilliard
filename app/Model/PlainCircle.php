<?php
App::uses('AppModel', 'Model');

/**
 * PlainCircle Model
 *
 * This class should be used for a specific situation
 * when there is no way to get data WITHOUT any associations in Cake standard ways.
 * because there could be a problem with CakePHP itself or ExtContainableBehavior.
 */
class PlainCircle extends AppModel {
    public $useTable = 'circles';
    
    public function getMembers(string $id) {
        $members = $this->find('all', [
            'conditions' => ['PlainCircle.id' => $id],
            'joins'      => [
                [
                    'table' => 'circle_members',
                    'alias' => 'CircleMember',
                    'foreignKey' => false,
                    'conditions'=> [
                        'PlainCircle.id = CircleMember.circle_id',
                    ]
                ],
            ],
            'fields'    => [
                'CircleMember.circle_id',
                'CircleMember.id',
                'CircleMember.user_id'
            ]
        ]);
        return $members;
    }
}

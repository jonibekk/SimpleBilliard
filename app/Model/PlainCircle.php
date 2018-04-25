<?php
App::uses('AppModel', 'Model');
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

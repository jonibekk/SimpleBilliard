<?php
App::uses('AppModel', 'Model');

use Goalous\Enum as Enum;

/**
 * PlainCircle Model
 * This class should be used for a specific situation
 * when there is no way to get data WITHOUT any associations in Cake standard ways.
 * because there could be a problem with CakePHP itself or ExtContainableBehavior.
 */
class PlainCircle extends AppModel
{
    public $useTable = 'circles';

    public function getMembers(int $circleId): array
    {
        $members = $this->find('all', [
            'joins'  => [
                [
                    'table'      => 'circle_members',
                    'alias'      => 'CircleMember',
                    'foreignKey' => false,
                    'conditions' => [
                        'PlainCircle.id = CircleMember.circle_id',
                        'PlainCircle.id'       => $circleId,
                        'CircleMember.del_flg' => false
                    ]
                ],
                [
                    'table'      => 'team_members',
                    'alias'      => 'TeamMember',
                    'conditions' => [
                        'CircleMember.team_id = TeamMember.team_id',
                        'CircleMember.user_id = TeamMember.user_id',
                        'TeamMember.status'  => Enum\Model\TeamMember\Status::ACTIVE,
                        'TeamMember.del_flg' => false,
                    ]
                ],
                [
                    'table'      => 'users',
                    'alias'      => 'User',
                    'conditions' => [
                        'TeamMember.user_id = User.id',
                        'User.active_flg' => true,
                        'User.del_flg'    => false,
                    ]
                ],
            ],
            'fields' => [
                'CircleMember.user_id'
            ]
        ]);

        if (empty($members)) {
            return [];
        }
        return Hash::extract($members, '{n}.CircleMember.user_id');
    }
}

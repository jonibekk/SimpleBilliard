<?php

class TeamMemberData
{
    public $name = 'TeamMember';

    public $records = [
        [
            'team_id'   => '1',
            'user_id'   => '1',
            'admin_flg' => '1',
            'status'    => '1',
            'member_no' => 'member_admin_team_a'
        ],
        [
            'team_id'   => '2',
            'user_id'   => '1',
            'admin_flg' => '1',
            'status'    => '1',
            'member_no' => 'member_admin_team_b'
        ],
        [
            'team_id'       => '1',
            'user_id'       => '2',
            'status'        => '1',
            'member_no'     => 'member_a_team_a',
            'coach_user_id' => '1',
        ],
        [
            'team_id'       => '1',
            'user_id'       => '3',
            'status'        => '1',
            'member_no'     => 'member_b_team_a',
            'coach_user_id' => '1',
        ],
        [
            'team_id'   => '1',
            'user_id'   => '4',
            'status'    => '1',
            'member_no' => 'member_c_team_a'
        ],
        [
            'team_id'   => '1',
            'user_id'   => '5',
            'status'    => '1',
            'member_no' => 'member_d_team_a'
        ],
        [
            'team_id'   => '2',
            'user_id'   => '6',
            'status'    => '1',
            'member_no' => 'member_e_team_b'
        ],
        [
            'team_id'   => '2',
            'user_id'   => '7',
            'status'    => '1',
            'member_no' => 'member_f_team_b'
        ],
        [
            'team_id'   => '2',
            'user_id'   => '8',
            'status'    => '1',
            'member_no' => 'member_g_team_b'
        ],
    ];
}

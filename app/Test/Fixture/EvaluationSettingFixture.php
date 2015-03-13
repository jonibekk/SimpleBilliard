<?php

/**
 * EvaluationSettingFixture

 */
class EvaluationSettingFixture extends CakeTestFixture
{

    /**
     * Fields
     *
     * @var array
     */
    public $fields = array(
        'id'                                  => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'primary', 'comment' => 'ID'),
        'team_id'                             => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'チームID(belongsToでTeamモデルに関連)'),
        'enable_flg'                          => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '評価 on/off'),
        'self_flg'                            => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => '自己評価 on/off'),
        'self_goal_score_flg'                 => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => '自己評価ゴールスコア on/off'),
        'self_goal_score_required_flg'        => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => '自己評価ゴールスコア必須 on/off'),
        'self_goal_comment_flg'               => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => '自己評価 ゴール コメント on/off'),
        'self_goal_comment_required_flg'      => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => '自己評価 ゴール コメント必須 on/off'),
        'self_score_flg'                      => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => '自己評価 トータル スコア on/off'),
        'self_score_required_flg'             => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => '自己評価 トータル スコア 必須 on/off'),
        'self_comment_flg'                    => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => '自己評価 トータル コメント on/off'),
        'self_comment_required_flg'           => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => '自己評価 トータル コメント 必須 on/off'),
        'evaluator_flg'                       => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => '評価者評価 on/off'),
        'evaluator_goal_score_flg'            => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => '評価者評価 ゴール スコア on/off'),
        'evaluator_goal_score_reuqired_flg'   => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => '評価者評価 ゴール スコア必須 on/off'),
        'evaluator_goal_comment_flg'          => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => '評価者評価 ゴール コメント on/off'),
        'evaluator_goal_comment_required_flg' => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => '評価者評価 ゴール コメント必須 on/off'),
        'evaluator_score_flg'                 => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => '評価者評価 トータル スコア on/off'),
        'evaluator_score_required_flg'        => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => '評価者評価 トータル スコア 必須 on/off'),
        'evaluator_comment_flg'               => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => '評価者評価 トータル コメント on/off'),
        'evaluator_comment_required_flg'      => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => '評価者評価 トータル コメント 必須 on/off'),
        'final_flg'                           => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => '最終評価者評価 on/off'),
        'final_score_flg'                     => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => '最終評価者評価 トータル スコア on/off'),
        'final_score_required_flg'            => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => '最終評価者評価 トータル スコア 必須 on/off'),
        'final_comment_flg'                   => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => '最終評価者評価 トータル コメント on/off'),
        'final_comment_required_flg'          => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => '最終評価者評価 トータル コメント 必須 on/off'),
        'leader_flg'                          => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => 'リーダ評価 on/off'),
        'leader_goal_score_flg'               => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => 'リーダ評価 ゴール スコア on/off'),
        'leader_goal_score_reuqired_flg'      => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => 'リーダ評価 ゴール スコア必須 on/off'),
        'leader_goal_comment_flg'             => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => 'リーダ評価 ゴール コメント on/off'),
        'leader_goal_comment_required_flg'    => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => 'リーダ評価 ゴール コメント必須 on/off'),
        'del_flg'                             => array('type' => 'boolean', 'null' => false, 'default' => '0', 'key' => 'index', 'comment' => '削除フラグ'),
        'deleted'                             => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '削除した日付時刻'),
        'created'                             => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => '追加した日付時刻'),
        'modified'                            => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '更新した日付時刻'),
        'indexes'                             => array(
            'PRIMARY' => array('column' => 'id', 'unique' => 1),
            'team_id' => array('column' => 'team_id', 'unique' => 0),
            'del_flg' => array('column' => 'del_flg', 'unique' => 0),
            'created' => array('column' => 'created', 'unique' => 0)
        ),
        'tableParameters'                     => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
    );

    /**
     * Records
     *
     * @var array
     */
    public $records = [
        [
            'id'                                  => '1',
            'team_id'                             => '1',
            'enable_flg'                          => true,
            'self_flg'                            => true,
            'self_goal_score_flg'                 => true,
            'self_goal_score_required_flg'        => true,
            'self_goal_comment_flg'               => true,
            'self_goal_comment_required_flg'      => true,
            'self_score_flg'                      => true,
            'self_score_required_flg'             => true,
            'self_comment_flg'                    => true,
            'self_comment_required_flg'           => true,
            'evaluator_flg'                       => true,
            'evaluator_goal_score_flg'            => true,
            'evaluator_goal_score_reuqired_flg'   => true,
            'evaluator_goal_comment_flg'          => true,
            'evaluator_goal_comment_required_flg' => true,
            'evaluator_score_flg'                 => true,
            'evaluator_score_required_flg'        => true,
            'evaluator_comment_flg'               => true,
            'evaluator_comment_required_flg'      => true,
            'final_flg'                           => true,
            'final_score_flg'                     => true,
            'final_score_required_flg'            => true,
            'final_comment_flg'                   => true,
            'final_comment_required_flg'          => true,
            'leader_flg'                          => true,
            'leader_goal_score_flg'               => true,
            'leader_goal_score_reuqired_flg'      => true,
            'leader_goal_comment_flg'             => true,
            'leader_goal_comment_required_flg'    => true,
            'del_flg'                             => false,
            'deleted'                             => null,
            'created'                             => '1426209229',
            'modified'                            => '1426209229'
        ]

    ];

}

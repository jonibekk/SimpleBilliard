<?php
/**
 * Created by PhpStorm.
 * User: raharjas
 * Date: 16/03/2018
 * Time: 11:05
 */

class EvaluatorChangeLogsFixture extends CakeTestFixtureEx
{

    public $fields = array(
        'id'                  => array(
            'type'     => 'biginteger',
            'null'     => false,
            'default'  => null,
            'unsigned' => false,
            'key'      => 'primary'
        ),
        'team_id'             => array(
            'type'     => 'biginteger',
            'null'     => false,
            'default'  => null,
            'unsigned' => true
        ),
        'last_update_user_id' => array(
            'type'     => 'biginteger',
            'null'     => false,
            'default'  => null,
            'unsigned' => true
        ),
        'evaluatee_user_id'   => array(
            'type'     => 'biginteger',
            'null'     => false,
            'default'  => null,
            'unsigned' => true,
            'key'      => 'index'
        ),
        'evaluator_user_ids'  => array(
            'type'    => 'string',
            'null'    => false,
            'default' => null,
            'length'  => 1000,
            'collate' => 'utf8mb4_general_ci',
            'charset' => 'utf8mb4'
        ),
        'del_flg'             => array('type' => 'boolean', 'null' => false, 'default' => '0'),
        'deleted'             => array(
            'type'     => 'integer',
            'null'     => true,
            'default'  => null,
            'unsigned' => true
        ),
        'created'             => array(
            'type'     => 'integer',
            'null'     => true,
            'default'  => null,
            'unsigned' => true
        ),
        'modified'            => array(
            'type'     => 'integer',
            'null'     => true,
            'default'  => null,
            'unsigned' => true
        ),
        'indexes'             => array(
            'PRIMARY'                                  => array('column' => 'id', 'unique' => 1),
            'evaluators_history_user_id_team_id_index' => array(
                'column' => array(
                    'evaluatee_user_id',
                    'team_id'
                ),
                'unique' => 0
            ),
        ),
    );
}
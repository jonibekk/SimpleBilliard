<?php

App::uses('CakeTestFixtureEx', 'Test/Fixture');

class TeamSsoSettingFixture extends CakeTestFixtureEx
{
    public $fields = [
        'id'              => array(
            'type'     => 'biginteger',
            'null'     => false,
            'default'  => null,
            'unsigned' => true,
            'key'      => 'primary'
        ),
        'team_id'         => array(
            'type'     => 'biginteger',
            'null'     => false,
            'default'  => null,
            'unsigned' => true,
            'key'      => 'unique',
            'comment'  => 'Team ID'
        ),
        'endpoint'        => array(
            'type'    => 'string',
            'null'    => false,
            'default' => null,
            'length'  => 2000,
            'collate' => 'utf8mb4_general_ci',
            'comment' => 'SAML2.0 Endpoint URL',
            'charset' => 'utf8mb4'
        ),
        'idp_issuer'      => array(
            'type'    => 'string',
            'null'    => false,
            'default' => null,
            'length'  => 2000,
            'collate' => 'utf8mb4_general_ci',
            'comment' => 'IdP Entity ID',
            'charset' => 'utf8mb4'
        ),
        'public_cert'     => array(
            'type'    => 'string',
            'null'    => false,
            'default' => null,
            'length'  => 2000,
            'collate' => 'utf8mb4_general_ci',
            'comment' => 'x.509 Public certificate of IdP',
            'charset' => 'utf8mb4'
        ),
        'del_flg'         => array('type' => 'boolean', 'null' => false, 'default' => '0'),
        'deleted'         => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true),
        'created'         => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true),
        'modified'        => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true),
        'indexes'         => array(
            'PRIMARY' => array('column' => 'id', 'unique' => 1),
            'team_id' => array('column' => 'team_id', 'unique' => 1)
        ),
        'tableParameters' => array('charset' => 'utf8mb4', 'collate' => 'utf8mb4_general_ci', 'engine' => 'InnoDB')
    ];

    public $records = [];

}

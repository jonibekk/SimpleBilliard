<?php

class DATABASE_CONFIG
{

    public $default = array(
        'datasource' => 'Database/Mysql',
        'persistent' => false,
        'host'       => 'localhost',
        'login'      => 'root',
        'password'   => '',
        'database'   => 'myapp',
    );

    public $test = array(
        'datasource' => 'Database/Mysql',
        'persistent' => false,
        'host'       => 'localhost',
        'login'      => 'root',
        'password'   => '',
        'database'   => 'myapp_test',
    );

    public function __construct()
    {
        //opsworksの環境の場合はdb設定をopsworks側で管理されているものに置き換える
        if (PUBLIC_ENV && file_exists(ROOT . DS . 'opsworks.php')) {
            require_once(ROOT . DS . 'opsworks.php');
            /** @noinspection PhpUndefinedClassInspection */
            $ow = new OpsWorks();
            $this->default['host'] = $ow->db->host;
            $this->default['database'] = $ow->db->database;
            $this->default['login'] = $ow->db->username;
            $this->default['password'] = $ow->db->password;
        }
    }
}

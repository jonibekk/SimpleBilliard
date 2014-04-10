<?php

class DATABASE_CONFIG
{

    public $default = array(
        'datasource' => 'Database/Mysql',
        'persistent' => false,
        'host' => 'localhost',
        'login' => 'root',
        'password' => '',
        'database' => 'myapp',
    );

    public $test = array(
        'datasource' => 'Database/Mysql',
        'persistent' => false,
        'host' => 'localhost',
        'login' => 'root',
        'password' => '',
        'database' => 'myapp_test',
    );

    public function __construct()
    {
        //opsworksの環境の場合はdb設定をopsworks側で管理されているものに置き換える
        if (file_exists(ROOT . DS . 'opsworks.php')) {
            require_once(ROOT . DS . 'opsworks.php');
            /** @noinspection PhpUndefinedClassInspection */
            $ow = new OpsWorks();
            $this->default['host'] = $ow->db->host;
            $this->default['database'] = $ow->db->database;
            $this->default['login'] = $ow->db->username;
            $this->default['password'] = $ow->db->password;
        }
        //werckerの場合(Test用DBを書き換え)
        $wercker = env('WERCKER_MYSQL_HOST');
        if($wercker){
            $this->test['host'] = env('WERCKER_MYSQL_HOST');
            $this->test['database'] = env('WERCKER_MYSQL_DATABASE');
            $this->test['login'] = env('WERCKER_MYSQL_USERNAME');
            $this->test['password'] = env('WERCKER_MYSQL_PASSWORD');
        }

    }
}

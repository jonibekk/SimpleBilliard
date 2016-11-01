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
        'encoding'   => 'utf8mb4',
        'unix_socket' => '/var/run/mysqld/mysqld.sock',
    );

    public $test = array(
        'datasource' => 'Database/Sqlite',
        'persistent' => false,
        'database'   => ':memory:',
        'prefix'     => '',
        'encoding'   => 'utf8mb4',

        //        'datasource' => 'Database/Mysql',
        //        'persistent' => false,
        //        'host'       => 'localhost',
        //        'login'      => 'root',
        //        'password'   => '',
        //        'database'   => 'myapp_test',
    );

    public $bench = array(
        'datasource' => 'Database/Mysql',
        'persistent' => false,
        'host'       => 'localhost',
        'login'      => 'root',
        'password'   => '',
        'database'   => 'myapp_bench',
        'encoding'   => 'utf8mb4',
    );

    public $redis = array(
        'datasource'  => 'Redis.RedisSource',
        'host'        => REDIS_HOST,
        'port'        => 6379,
        'password'    => '',
        'database'    => 0,
        'timeout'     => 0,
        'persistent'  => false,
        'unix_socket' => '',
        'prefix'      => '',
    );

    public $redis_test = array(
        'datasource'  => 'Redis.RedisSource',
        'host'        => REDIS_HOST,
        'port'        => 6379,
        'password'    => '',
        'database'    => 0,
        'timeout'     => 0,
        'persistent'  => false,
        'unix_socket' => '',
        'prefix'      => 'test:',
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
        //set prefix of redis
        if (isset($this->redis)) {
            $this->redis['prefix'] = ENV_NAME . ":";
        }
        //set prefix of redis_test
        if (isset($this->redis_test)) {
            $this->redis_test['prefix'] = ENV_NAME . ":" . $this->redis_test['prefix'];
        }
        // Selenium経由の場合defaultを参照するのでIPで振り分ける
        // 仮想環境経由:192.168.50.1, ローカル環境経由:127.0.0.1
//        if (stristr(env('HTTP_USER_AGENT'), 'selenium') && env('REMOTE_ADDR') === '192.168.50.1') {
//            $this->default = $this->test;
//        }
    }
}

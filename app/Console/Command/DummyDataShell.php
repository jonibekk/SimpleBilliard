<?php
App::uses('CakeSchema', 'Model');

/**
 * ダミーデータ登録用スクリプト
 * Created by PhpStorm.
 * User: bigplants
 * Date: 5/16/14
 * Time: 2:49 PM
 *
 * @property User $User
 * @property Team $Team
 */
class DummyDataShell extends AppShell
{

    var $uses = [
        'User',
        'Team',
    ];

    /**
     * @var CakeSchema $Schema
     */
    public $Schema;

    public $records = [];

    public $start_time;

    public $table;

    public $digits;

    function startup()
    {
        Configure::write('debug', 2);
        ini_set('memory_limit', '2024M');
        Configure::write('shell_mode', true);
        $this->_setTableAndRecords();
        $this->start_time = microtime(true);

        $this->table = "all";
        $this->digits = 8;
    }

    public function getOptionParser()
    {
        $parser = parent::getOptionParser();
        $options = [
            'config' => ['short' => 'c', 'help' => 'DBのConfig名', 'required' => false],
            'table'  => ['short' => 't', 'help' => 'テーブル名（指定なしの場合は全テーブル）', 'required' => false],
            'digits' => ['short' => 'd', 'help' => '挿入データの桁数 2〜9(デフォルトは7)', 'required' => false],
        ];
        $parser->addOptions($options);
        return $parser;
    }

    function main()
    {
        $this->User->cacheQueries = false;
        if ($this->params['config']) {
            $this->User->useDbConfig = $this->params['config'];
        }
        else {
            $this->User->useDbConfig = "bench";
        }

        if ($this->params['table']) {
            $this->table = $this->params['table'];
        }
        if ($this->params['digits']) {
            $this->digits = $this->params['digits'];
        }

        foreach ($this->records as $table_name => $records) {
            //全指定以外でかつテーブル名が違う場合はスキップ
            if ($this->table !== "all" && $this->table !== $table_name) {
                continue;
            }
            $this->insertInitData($records, $table_name);
        }
        foreach ($this->records as $table_name => $records) {
            //全指定以外でかつテーブル名が違う場合はスキップ
            if ($this->table !== "all" && $this->table !== $table_name) {
                continue;
            }
            $this->insertMulti($records, $table_name);
        }
        $end_time = microtime(true);
        $total_time = round($end_time - $this->start_time, 2);

        $this->out("****************************");
        $this->out("ダミーデータの登録が完了しました。");
        $this->out("実行時間:{$total_time}sec");
    }

    public function deleteAllDummy()
    {
        $this->User->deleteAll(['User.id Like' => "dummy_%"]);
    }

    public function insertInitData($default_data, $table_name)
    {
        $fields = array_keys($default_data);
        $holder = '(' . implode(',', array_fill(0, count($fields), '?')) . ')';
        $once_rows = 10;
        $holders = implode(',', array_fill(0, $once_rows, $holder));
        $current_no = 1;
        $data = [];
        for ($i = 0; $i < $once_rows; $i++) {
            $data[] = $default_data;
        }
        $params = [];
        foreach ($data as $val) {
            foreach ($fields as $field) {
                if ($field == "id") {
                    $params[] = "null";
                }
                else {
                    $params[] = $val[$field];
                }
            }
            $current_no++;
        }
        $fields_imploded = implode(',', $fields);
        $sql = "INSERT INTO {$table_name} ({$fields_imploded}) VALUES {$holders}";
        $this->User->query($sql, $params);
    }

    public function insertMulti($default_data, $table_name)
    {
        $schema = $this->Schema->read();
        $table_schema = $schema['tables'][$table_name];
        $current_start_time = microtime(true);

        $fields = array_keys($default_data);
        $fields_imploded = implode(',', $fields);

        $from = "";
        $unique_num = "";
        for ($i = 1; $i < $this->digits; $i++) {
            if ($i != 1) {
                $from .= ",";
                $unique_num .= "+";
            }
            $from .= " {$table_name} t{$i}";
            $unique_num .= "t{$i}.id * {$i} ";
        }
        $select_fields = "";
        $datetime_list = [
            'created',
            'modified',
            'last_login',
            'password_modified',
            'email_token_expires',
            'sent_datetime',

        ];
        foreach ($default_data as $key => $val) {
            if ($key === "id") {
                $select_fields .= 'null';
                continue;
            }
            if ($key === "item") {
                $select_fields .= ', null';
            }
            elseif ($table_schema[$key]['type'] == "string" || $table_schema[$key]['type'] == "text") {
                $select_fields .= ", CONCAT(t1.{$key},({$unique_num}))";
            }
            elseif (in_array($key, $datetime_list)) {
                $select_fields .= ", unix_timestamp() - ({$unique_num})";
            }
            else {
                $select_fields .= ", t1.{$key}";
            }
        }
        $sql = "INSERT INTO {$table_name} ({$fields_imploded}) SELECT {$select_fields} FROM {$from}";
        $this->User->query($sql);
        $end_time = microtime(true);
        $current_time = round($end_time - $current_start_time, 2);
        $total_time = round($end_time - $this->start_time, 2);
        $this->out("****************************");
        $this->out("完了 : {$table_name}");
        $this->out("実行時間:{$current_time}sec");
        $this->out("経過時間:{$total_time}sec");

    }

    function _setTableAndRecords()
    {
        $this->Schema = new CakeSchema();
        $schema = $this->Schema->read();
        $records = [];
        foreach ($schema['tables'] as $table_name => $fields) {
            if ($table_name == "missing") {
                continue;
            }
            foreach ($fields as $field_name => $field) {
                if ($field_name == "indexes") {
                    continue;
                }
                if ($field_name == "tableParameters") {
                    continue;
                }
                if (!isset($field['type'])) {
                    continue;
                }

                if ($field_name == "del_flg") {
                    $records[$table_name][$field_name] = false;
                }
                elseif ($field_name == "deleted") {
                    $records[$table_name][$field_name] = null;
                }
                else {

                    if ($field['type'] == "string") {
                        $records[$table_name][$field_name] = "test_string";
                    }
                    elseif ($field['type'] == "integer") {
                        $records[$table_name][$field_name] = true;

                    }
                    elseif ($field['type'] == "text") {
                        $records[$table_name][$field_name] = "test_text";
                    }
                    elseif ($field['type'] == "boolean") {
                        $records[$table_name][$field_name] = true;
                    }
                    elseif ($field['type'] == "datetime") {
                        $records[$table_name][$field_name] = date('Y-m-d H:i:s');
                    }
                    elseif ($field['type'] == "date") {
                        $records[$table_name][$field_name] = date('Y-m-d');
                    }
                    else {
                        $records[$table_name][$field_name] = false;
                    }
                }
            }
        }
        $this->records = $records;
    }
}

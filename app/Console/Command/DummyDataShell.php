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

    function startup()
    {
        Configure::write('debug', 2);
        ini_set('memory_limit', '2024M');
        Configure::write('shell_mode', true);
        $this->_setTableAndRecords();
        $this->start_time = microtime(true);
    }

    public function getOptionParser()
    {
        $parser = parent::getOptionParser();
        $commands = [
            'send_mail_by_id' => [
                'help'   => 'SendMailのidを元にメールを送信する',
                'parser' => [
                    'options' => [
                        'config' => ['short' => 'c', 'help' => 'DBのConfig名', 'required' => false,],
                    ]
                ]
            ],
        ];
        $parser->addSubcommands($commands);
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
        foreach ($this->records as $table_name => $records) {
            $this->insertInitData($records, $table_name);
        }
        foreach ($this->records as $table_name => $records) {
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
                    $params[] = $current_no;
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
        for ($i = 1; $i < 7; $i++) {
            if ($i != 1) {
                $from .= ",";
            }
            $from .= " {$table_name} t{$i}";
        }
        $id = "t6.id * 100000 + t5.id * 10000 + t4.id * 1000 + t3.id * 100 + t2.id * 10 + t1.id";
        $select_fields = "";
        foreach ($default_data as $key => $val) {
            if ($key === "id") {
                continue;
            }
            if ($table_schema[$key]['type'] == "string" || $table_schema[$key]['type'] == "text") {
                $select_fields .= ", CONCAT(t1.{$key},({$id}))";
            }
            else {
                $select_fields .= ", t1.{$key}";
            }
        }
        $sql = "INSERT INTO {$table_name} ({$fields_imploded}) SELECT {$id}{$select_fields} FROM {$from}";
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
                        $records[$table_name][$field_name] = "test string";
                    }
                    elseif ($field['type'] == "integer") {
                        $records[$table_name][$field_name] = true;

                    }
                    elseif ($field['type'] == "text") {
                        $records[$table_name][$field_name] = "test text";
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

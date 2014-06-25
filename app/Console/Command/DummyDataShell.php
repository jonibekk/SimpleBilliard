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

    function startup()
    {
        Configure::write('debug', 0);
        ini_set('memory_limit', '2024M');
        Configure::write('shell_mode', true);
    }

    function main()
    {

//        $this->Schema = new CakeSchema;
//        $this->Schema->read();
        $this->User->cacheQueries = false;
        $start_time = microtime(true);
        $this->insertMultiMusic($this->records, "users", 1000000);
        $end_time = microtime(true);
        $total_time = $end_time - $start_time;

//        $data = [];
//        for ($i = 0; $i < 10000; $i++) {
//            $this->user_record['id'] = "dummy_" . $i;
//            $data[] = $this->user_record;
//        }
//        $this->User->saveAll($data, ['validate' => false, 'atomic' => false]);
        $this->out("ダミーデータを登録しました。");
        $this->out("実行時間:{$total_time}sec");
    }

    public $records = [
        'id'                => '537ce224-f96c-4c97-89ea-433dac11b50b',
        'first_name'        => 'Lorem ipsum dolor sit amet',
        'last_name'         => 'Lorem ipsum dolor sit amet',
        'middle_name'       => 'Lorem ipsum dolor sit amet',
        'gender_type'       => 3,
        'birth_day'         => '2014-05-22',
        'hide_year_flg'     => 1,
        'hometown'          => 'Lorem ipsum dolor sit amet',
        'comment'           => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
        'password'          => 'Lorem ipsum dolor sit amet',
        'password_token'    => 'Lorem ipsum dolor sit amet',
        'password_modified' => '2014-05-22 02:28:04',
        'no_pass_flg'       => 1,
        'photo_file_name'   => 'Lorem ipsum dolor sit amet',
        'primary_email_id'  => 'Lorem ipsum dolor sit amet',
        'active_flg'        => 1,
        'last_login'        => '2014-05-22 02:28:04',
        'admin_flg'         => 1,
        'default_team_id'   => 'Lorem ipsum dolor sit amet',
        'timezone'          => 3,
        'auto_timezone_flg' => 1,
        'language'          => 'Lorem ipsum dolor sit amet',
        'auto_language_flg' => 1,
        'romanize_flg'      => 1,
        'update_email_flg'  => 1,
        'del_flg'           => 0,
        'deleted'           => '2014-05-22 02:28:04',
        'created'           => '2014-05-22 02:28:04',
        'modified'          => '2014-05-22 02:28:04'
    ];

    public function deleteAllDummy()
    {
        $this->User->deleteAll(['User.id Like' => "dummy_%"]);
    }

    public function insertMultiMusic($default_data, $table_name, $row_count = 100000)
    {
        $fields = array_keys($default_data);
        $holder = '(' . implode(',', array_fill(0, count($fields), '?')) . ')';
        $once_rows = 10000;
        $query_count = floor($row_count / $once_rows);
        $holders = implode(',', array_fill(0, $once_rows, $holder));
        $current_no = 0;
        $data = [];
        for ($i = 0; $i < $once_rows; $i++) {
            $data[] = $default_data;
        }
        for ($i = 0; $i < $query_count; $i++) {
            $params = array();
            foreach ($data as $val) {
                foreach ($fields as $field) {
                    if ($field == 'created' || $field == 'modified') {
                        $params[] = date('Y-m-d H:i:s');
                    }
                    elseif ($field == "id") {
                        $params[] = "dummy_" . $current_no;
                    }
                    else {
                        $params[] = $val[$field];
                    }
                }
                $current_no++;
            }
            $fields_imploded = implode(',', $fields);
            $sql = "INSERT INTO {$table_name} ({$fields_imploded}) VALUES {$holders}";
            unset($fields_imploded);
            $this->User->query($sql, $params);
        }
    }
}

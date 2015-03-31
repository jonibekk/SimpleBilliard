<?

/**
 * Created by PhpStorm.
 * User: daikihirakata
 * Date: 15/04/01
 * Time: 2:28
 *
 * @property Evaluation $Evaluation
 */
class DataUpdateShell extends AppShell
{

    var $uses = array(
        'Evaluation',
    );
    public $start_time;

    function startup()
    {
        Configure::write('shell_mode', true);
        ini_set('memory_limit', '2024M');
        $this->start_time = microtime(true);
    }

    public function getOptionParser()
    {
        $parser = parent::getOptionParser();
        $commands = [
            'recovery_evaluations' => [
                'help' => 'Evaluationデータのリカバリ',
            ],
        ];
        $parser->addSubcommands($commands);
        return $parser;
    }

    public function main()
    {
        $this->out($this->getOptionParser()->help());
    }

    public function recovery_evaluations()
    {
        $save_data = [];
        $options = [
            'fields' => ['id', 'team_id', 'evaluatee_user_id', 'evaluate_term_id'],
            'group'  => ['team_id', 'evaluatee_user_id', 'evaluate_term_id'],
        ];
        $grouped_evals = $this->Evaluation->find('all', $options);
        $grouped_evals = Hash::combine($grouped_evals, '{n}.Evaluation.id', '{n}.Evaluation');

        foreach ($grouped_evals as $evals) {
            unset($evals['id']);
            $data = $this->Evaluation->find('all', ['conditions' => $evals]);
            $data = Hash::combine($data, '{n}.Evaluation.id', '{n}.Evaluation');
            //index採番
            $index_num = 0;
            foreach ($data as $key => $v) {
                $data[$key]['index_num'] = $index_num++;
            }
            //reset my_turn_flg
            foreach ($data as $key => $v) {
                $data[$key]['my_turn_flg'] = false;
            }
            //resetting my_turn_flg
            //TODO for only goal_id is null temporarily
            foreach ($data as $key => $v) {
                if (!is_null($v['goal_id'])) {
                    continue;
                }
                if ($v['status'] != Evaluation::TYPE_STATUS_DONE) {
                    $data[$key]['my_turn_flg'] = true;
                    break;
                }
            }
            $save_data += $data;
        }
        if (!empty($save_data)) {
            $this->Evaluation->saveAll($save_data);
        }
        $this->hr(1);
        if (!empty($save_data)) {
            $this->out("Updated Evaluation.");
        }
        else{
            $this->out("No Data.");
        }

        $total_time = round(microtime(true) - $this->start_time, 2);
        $count = count($save_data);
        $this->out("Total Updated Record Count: {$count}");
        $this->out("Total Time: {$total_time}sec");
    }
}

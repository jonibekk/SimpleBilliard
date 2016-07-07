<?php

/**
 * Created by PhpStorm.
 * User: daikihirakata
 *
 * @property Team       $Team
 * @property User       $User
 * @property TeamMember $TeamMember
 */
class CsOperationShell extends AppShell
{

    public $start_time;

    public $uses = array(
        'Team',
        'User',
        'TeamMember'
    );

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
            'user_withdrawal' => [
                'help'   => 'ユーザ退会処理',
                'parser' => [
                    'options' => [
                        'user_id' => ['short' => 'u', 'help' => 'ユーザID', 'required' => true,],
                    ]
                ]
            ],
        ];
        $parser->addSubcommands($commands);
        return $parser;
    }

    public function main()
    {
        $this->out($this->getOptionParser()->help());
    }

    /**
     * ユーザ退会処理
     * ユーザ退会ができる条件は、チームに一つも属していない(inactiveでもダメ)
     */
    public function user_withdrawal()
    {
        $this->hr(1);
        if (!isset($this->params['user_id']) || !$this->params['user_id']) {
            $this->out('ユーザIDは必須です。例: -u 111');
            return;
        }
        $user_id = $this->params['user_id'];
        $teams = $this->TeamMember->getAllTeam($user_id, true);
        if (!empty($teams)) {
            $this->out('以下のチームにまだ所属している為、処理できません。');
            $this->hr(0);
            $this->out($teams);
            $this->hr(0);
            return;
        }

        $total_time = round(microtime(true) - $this->start_time, 2);
        $this->out("Total Time: {$total_time}sec");
    }
}

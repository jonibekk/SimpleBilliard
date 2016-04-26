<?php

/**
 * 各種データ集計用バッチ
 * Console/cake insight -d YYYY-MM-DD -t timezone
 *
 * @property Team          $Team
 * @property TeamMember    $TeamMember
 * @property MemberGroup   $MemberGroup
 * @property Circle        $Circle
 * @property CircleMember  $CircleMember
 * @property TeamInsight   $TeamInsight
 * @property GroupInsight  $GroupInsight
 * @property CircleInsight $CircleInsight
 * @property GlRedis       $GlRedis
 * @property AccessUser    $AccessUser
 */
class SetupGuideShell extends AppShell
{
    public $uses = array(
        'Team',
        'TeamMember',
        'MemberGroup',
        'Circle',
        'CircleMember',
        'TeamInsight',
        'GroupInsight',
        'CircleInsight',
        'AccessUser',
        'GlRedis',
    );

    public function startup()
    {
        parent::startup();
    }

    public function getOptionParser()
    {
        $parser = parent::getOptionParser();
        $options = [
            'date'     => ['short' => 'd', 'help' => '集計日(YYYY-MM-DD)', 'required' => true,],
            'timezone' => ['short' => 't', 'help' => 'タイムゾーン', 'required' => true,],
        ];
        $parser->addOptions($options);
        return $parser;
    }

    public function main()
    {
        echo "HOGEHOGE\n";
    }

    protected function _usageString()
    {
        return 'Usage: cake insight YYYY-MM-DD time_offset';
    }

    protected function _setupModels($team_id)
    {
        foreach ($this->uses as $model) {
            $this->{$model}->current_team_id = $team_id;
        }
    }

}

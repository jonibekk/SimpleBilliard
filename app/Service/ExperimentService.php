<?php
/**
 * Created by PhpStorm.
 * User: bigplants
 * Date: 2016/10/25
 * Time: 14:42
 */

App::import('Service', 'AppService');
App::uses('Experiment', 'Model');

class ExperimentService extends AppService
{
    /**
     * 定義済みの実験かどうかを返す
     * 頻繁に参照されるため、キャッシュを利用
     * FORCE_ENABLE_ALL_EXPERIMENTSがtrueの場合は全てtrueを返す
     * FORCE_DISABLE_ALL_EXPERIMENTSがtrueの場合は全てfalseを返す。FORCE_ENABLE_ALL_EXPERIMENTSより優先される
     *
     * @param $name
     *
     * @param int|null $teamId
     * @return bool
     */
    function isDefined($name, ?int $teamId = null)
    {
        if (defined('FORCE_DISABLE_ALL_EXPERIMENTS') && FORCE_DISABLE_ALL_EXPERIMENTS) {
            return false;
        }
        if (defined('FORCE_ENABLE_ALL_EXPERIMENTS') && FORCE_ENABLE_ALL_EXPERIMENTS) {
            return true;
        }
        /** @var  Experiment $Experiment */
        $Experiment = ClassRegistry::init('Experiment');
        if (!empty($teamId)) {
            $Experiment->current_team_id = $teamId;
        }
        $res = Cache::read($Experiment->getCacheKey(CACHE_KEY_EXPERIMENT . ":" . $name), 'team_info');
        if ($res !== false) {
            return (bool)!empty($res);
        }

        $res = $Experiment->findExperiment($name);
        Cache::write($Experiment->getCacheKey(CACHE_KEY_EXPERIMENT . ":" . $name), $res, 'team_info');
        return (bool)!empty($res);
    }
}

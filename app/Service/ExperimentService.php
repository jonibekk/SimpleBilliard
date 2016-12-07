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
     * ENABLE_ALL_EXPERIMENTSがtrueの場合は無条件にtrueを返す
     *
     * @param $name
     *
     * @return bool
     */
    function isDefined($name)
    {
        if (defined('ENABLE_ALL_EXPERIMENTS') && ENABLE_ALL_EXPERIMENTS) {
            return true;
        }
        /** @var  Experiment $Experiment */
        $Experiment = ClassRegistry::init('Experiment');
        $res = Cache::read($Experiment->getCacheKey(CACHE_KEY_EXPERIMENT . ":" . $name), 'team_info');
        if ($res !== false) {
            return (bool)!empty($res);
        }

        $res = $Experiment->findExperiment($name);
        Cache::write($Experiment->getCacheKey(CACHE_KEY_EXPERIMENT . ":" . $name), $res, 'team_info');
        return (bool)!empty($res);
    }
}

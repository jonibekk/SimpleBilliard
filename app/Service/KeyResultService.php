<?php
App::import('Service', 'AppService');

/**
 * Class KeyResultService
 */
class KeyResultService extends AppService
{
    function buildKrUnitsSelectList($units_config)
    {
        $unit_select_list = [];
        foreach($units_config as $v) {
            $unit_select_list[$v['id']] = "{$v['label']}({$v['unit']})";
        }
        return $unit_select_list;
    }

}

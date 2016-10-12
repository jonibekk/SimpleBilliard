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

    /**
     * キーリザルト一覧を表示用に整形するためのラッパー
     * @param  array $key_results
     * @param  string $model_alias
     * @return array $key_results
     */
    function processKeyResults($key_results, $model_alias = 'KeyResult')
    {
        foreach($key_results as $k => $v) {
            $key_results[$k][$model_alias] = $this->processKeyResult($v[$model_alias]);
        }
        return $key_results;
    }

    /**
     * キーリザルトを表示用に整形
     * @param  array $key_result
     * @return array $key_result
     */
    function processKeyResult($key_result)
    {
        $unit_name_display = '';
        $start_value =  h((float)$key_result['start_value']);
        $target_value = h((float)$key_result['target_value']);

        $key_result['start_value'] = $start_value;
        $key_result['target_value'] = $target_value;

        if ($key_result['value_unit'] == KeyResult::UNIT_BINARY) {
            $key_result['display_value'] = __('Complete/Incomplete');
            return $key_result;
        }

        // in other unit case, skipping showing unit name
        if ($key_result['value_unit'] != KeyResult::UNIT_NUMBER) {
            $unit_name_display = KeyResult::$UNIT[$key_result['value_unit']];
        }
        $key_result['display_value'] = "{$start_value}{$unit_name_display} → {$target_value}{$unit_name_display}";

        return $key_result;
    }

}

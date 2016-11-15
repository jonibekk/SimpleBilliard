<?php
App::import('Service', 'AppService');

/**
 * Class KeyResultService
 */
class KeyResultService extends AppService
{
    /**
     * KRのValueUnitセレクトボックス値の生成
     * @return array $unit_select_list
     */
    function buildKrUnitsSelectList()
    {
        $units_config = Configure::read("label.units");
        $unit_select_list = [];
        foreach($units_config as $v) {
            $unit_select_list[$v['id']] = "{$v['label']}({$v['unit']})";
        }
        return $unit_select_list;
    }

    /**
     * キーリザルト一覧を表示用に整形するためのラッパー
     *
     * @param  array  $key_results
     * @param  string $model_alias
     * @param string  $symbol
     *
     * @return array $key_results
     */
    function processKeyResults($key_results, $model_alias = 'KeyResult', $symbol = '→')
    {
        foreach($key_results as $k => $v) {
            $key_results[$k][$model_alias] = $this->processKeyResult($v[$model_alias], $symbol);
        }
        return $key_results;
    }

    /**
     * キーリザルトを表示用に整形
     *
     * @param $keyResult
     * @param $symbol
     *
     * @return array $key_result
     * @internal param array $key_result
     */
    function processKeyResult($keyResult, $symbol = '→')
    {
        // 完了/未完了
        if ($keyResult['value_unit'] == KeyResult::UNIT_BINARY) {
            $keyResult['display_value'] = __('Complete/Incomplete');
            return $keyResult;
        }

        // 少数の不要な0を取り除く
        $keyResult['start_value'] =  h((float)$keyResult['start_value']);
        $keyResult['target_value'] = h((float)$keyResult['target_value']);

        // 単位を文頭におくか文末に置くか決める
        $unitName = KeyResult::$UNIT[$keyResult['value_unit']];
        $headUnit = '';
        $tailUnit = '';
        if (in_array($keyResult['value_unit'], KeyResult::$UNIT_HEAD)) {
            $headUnit = $unitName;
        }
        if (in_array($keyResult['value_unit'], KeyResult::$UNIT_TAIL)) {
            $tailUnit = $unitName;
        }

        $keyResult['display_value'] = "{$headUnit}{$keyResult['start_value']}{$tailUnit} {$symbol} {$headUnit}{$keyResult['target_value']}{$tailUnit}";

        return $keyResult;
    }

}

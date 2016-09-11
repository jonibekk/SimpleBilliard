<?php
App::uses('ApiController', 'Controller/Api');
/** @noinspection PhpUndefinedClassInspection */

/**
 * Created by PhpStorm.
 * User: daikihirakata
 * Date: 9/6/16
 * Time: 16:38
 *
 * @property Goal $Goal
 */
class GoalsController extends ApiController
{
    public $uses = [
        'Goal'
    ];

    function post_validate()
    {
        return $this->_getResponseDefaultValidation($this->Goal);
    }

    /**
     * Goal作成&編集においての初期化処理
     * formで利用する値を取得する
     *
     * @query_params bool categories
     * @query_params bool labels
     * @query_params bool term_types
     */
    function get_init_form()
    {
        /**
         * @var Label $Label
         */
        $Label = ClassRegistry::init('Label');
        $res = [];

        if ($this->request->query('categories') == true) {
            $res['categories'] = $this->Goal->GoalCategory->getCategories(['id', 'name']);
        }

        if ($this->request->query('labels') == true) {
            $res['labels'] = Hash::extract($Label->getListWithGoalCount(), '{n}.Label');
        }

        if ($this->request->query('term_types') == true) {
            $current = $this->Team->EvaluateTerm->getTermData(EvaluateTerm::TYPE_CURRENT);
            $current['type'] = 'current';
            $next = $this->Team->EvaluateTerm->getTermData(EvaluateTerm::TYPE_NEXT);
            $next['type'] = 'next';
            $res['terms'] = [$current, $next];
        }

        return $this->_getResponseSuccess($res);
    }
}

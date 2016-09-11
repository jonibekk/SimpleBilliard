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
     * @query_params bool tags
     */
    function get_init_form()
    {
        $res = [];

        if ($this->request->query('categories') == true) {
            $categories = $this->Goal->GoalCategory->getCategoryList();
            $res['categories'] = $categories;
        }

//        if ($this->request->query('tags') == true) {
//            $res['tags'] = true;
//        }

        return $this->_getResponseSuccess($res);
    }
}

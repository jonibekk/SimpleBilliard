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

    /**
     * ゴール(KR除く)のバリデーションAPI
     * 成功(Status Code:200)、失敗(Status Code:400)
     * - fieldsパラメタにカンマ区切りで検査対象フィールドを指定。allもしくは指定なしの場合はすべてのフィールドのvalidationを行う。
     *
     * @query_param fields
     * @return CakeResponse
     */
    function post_validate()
    {
        $fields = [];
        if ($this->request->query('fields')) {
            $fields = explode(',', $this->request->query('fields'));
            //allが含まれる場合はすべて指定。それ以外はそのまま
            $fields = in_array('all', $fields) ? [] : $fields;
        }
        $validation = $this->Goal->validateGoalPOST($this->request->data, $fields);
        if ($validation === true) {
            return $this->_getResponseSuccess();
        }
        $validationMsg = $this->_validationExtract($validation);
        return $this->_getResponseValidationFail($validationMsg);
    }

    /**
     * Goal作成&編集においての初期化処理API
     * formで利用する値を取得する
     *
     * @query_params bool data_types `all` is returning all data_types, it can be selected individually(e.g. `categories,labels`)
     * @return CakeResponse
     */
    function get_init_form()
    {
        /**
         * @var Label $Label
         */
        $Label = ClassRegistry::init('Label');
        $res = [];

        if ($this->request->query('data_types')) {
            $dataTypes = explode(',', $this->request->query('data_types'));
            if (in_array('all', $dataTypes)) {
                $dataTypes = 'all';
            }
        } else {
            $dataTypes = 'all';
        }

        if ($dataTypes == 'all' || in_array('categories', $dataTypes)) {
            $res['categories'] = Hash::extract(
                $this->Goal->GoalCategory->getCategories(['id', 'name']),
                '{n}.GoalCategory'
            );
        }

        if ($dataTypes == 'all' || in_array('labels', $dataTypes)) {
            $res['labels'] = Hash::extract($Label->getListWithGoalCount(), '{n}.Label');
        }

        if ($dataTypes == 'all' || in_array('terms', $dataTypes)) {
            $current = $this->Team->EvaluateTerm->getTermData(EvaluateTerm::TYPE_CURRENT);
            $current['type'] = 'current';
            //TODO 個別にdate formatしているが一括で変更する仕組みを考えたほうがいい
            $current['start_date'] = date('Y-m-d', $current['start_date'] + $current['timezone'] * HOUR);
            $current['end_date'] = date('Y-m-d', $current['end_date'] + $current['timezone'] * HOUR);
            $next = $this->Team->EvaluateTerm->getTermData(EvaluateTerm::TYPE_NEXT);
            $next['type'] = 'next';
            $next['start_date'] = date('Y-m-d', $next['start_date'] + $next['timezone'] * HOUR);
            $next['end_date'] = date('Y-m-d', $next['end_date'] + $next['timezone'] * HOUR);
            $res['terms'] = [$current, $next];
        }

        return $this->_getResponseSuccess($res);
    }

    /**
     * ゴール新規登録API
     * *必須フィールド
     * - socket_id: pusherへのpush用
     * *処理
     * - バリデーション(失敗したらレスポンス返す)
     * - ゴール新規登録(トランザクションかける。失敗したらレスポンス返す) TODO: タグの保存処理まだやっていない
     * - フィードへ新しい投稿がある旨を知らせる
     * - コーチへ通知
     * - セットアップガイドのステータスを更新
     * - コーチと自分の認定件数を更新(キャッシュを削除)
     * - Mixpanelでトラッキング
     * - TODO: 遷移先の情報は渡さなくて大丈夫か？api以外の場合はリダイレクトを分岐している。
     * - ゴールIDをレスポンスに含めて返却
     *
     * @return CakeResponse
     */
    function post()
    {
        $validateResult = $this->_validateCreateGoal($this->request->data);
        if ($validateResult !== true) {
            return $validateResult;
        }
        //TODO タグの保存処理まだ
        $this->Goal->begin();
        $isSaveSuccess = $this->Goal->add(
            [
                'Goal'      => $this->request->data,
                'KeyResult' => [Hash::get($this->request->data, 'key_result')],
                'Label'     => Hash::get($this->request->data, 'labels'),
            ]
        );
        if ($isSaveSuccess === false) {
            $this->Goal->rollback();
            return $this->_getResponseBadFail(__('Failed to save a goal.'));
        }
        $this->Goal->commit();

        $newGoalId = $this->Goal->getLastInsertID();

        //通知
        $this->NotifyBiz->push(Hash::get($this->request->data, 'socket_id'), "all");
        $this->_sendNotifyToCoach($newGoalId, NotifySetting::TYPE_MY_MEMBER_CREATE_GOAL);

        $this->updateSetupStatusIfNotCompleted();
        //コーチと自分の認定件数を更新(キャッシュを削除)
        $coach_id = $this->User->TeamMember->getCoachUserIdByMemberUserId($this->my_uid);
        if ($coach_id) {
            Cache::delete($this->Goal->getCacheKey(CACHE_KEY_UNAPPROVED_COUNT, true), 'user_data');
            Cache::delete($this->Goal->getCacheKey(CACHE_KEY_UNAPPROVED_COUNT, true, $coach_id), 'user_data');
        }

        $this->Mixpanel->trackGoal(MixpanelComponent::TRACK_CREATE_GOAL, $newGoalId);

        return $this->_getResponseSuccess(['goal_id' => $newGoalId]);
    }

    /**
     * ゴール作成のバリデーション
     * バリデーションエラーの場合はCakeResponseを返すのでaction methodもこれをそのまま返す
     * - key resultがなければバリデーションを通さずレスポンスを返す
     * - ゴールとKRのバリデーションは後ほど組み立てやすいようにそれぞれ別々に実行し、結果をマージしている。
     * TODO: 厳密にバリデーションルール、メッセージを再定義する
     *
     * @param array $data
     *
     * @return true|CakeResponse
     */
    function _validateCreateGoal($data)
    {
        if (!Hash::get($data, 'key_result')) {
            return $this->_getResponseBadFail(__('top Key Result is required!'));
        }

        $validation = [];

        $goal_validation = $this->Goal->validateGoalPOST($data);
        if ($goal_validation !== true) {
            $validation = $this->_validationExtract($goal_validation);
        }

        $kr_validation = $this->Goal->KeyResult->validateKrPOST($data['key_result']);
        if ($kr_validation !== true) {
            $validation['key_result'] = $this->_validationExtract($kr_validation);
        }

        if (!empty($validation)) {
            return $this->_getResponseBadFail(__('Validation failed.'), $validation);
        }
        return true;
    }
}

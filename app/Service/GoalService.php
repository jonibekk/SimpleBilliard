<?php
/**
 * Created by PhpStorm.
 * User: yoshidam2
 * Date: 2016/09/21
 * Time: 17:57
 */

App::uses('Goal', 'Model');
App::uses('Team', 'Model');
App::import('View', 'Helper/TimeExHelper');
App::import('View', 'Helper/UploadHelper');

class GoalService extends Object
{
    const EXTEND_GOAL_LABELS = "GOAL:EXTEND_GOAL_LABELS";
    const EXTEND_TOP_KEY_RESULT = "GOAL:EXTEND_TOP_KEY_RESULT";

    function get($id, $extends =[])
    {
        $Goal = ClassRegistry::init("Goal");
        $Team = ClassRegistry::init("Team");
        $TimeExHelper = new TimeExHelper(new View());

        $data = Hash::extract($Goal->findById($id), 'Goal');
        if (empty($data)) {
            return $data;
        }
        // 各サイズの画像URL追加
        $data = $this->attachImgUrl($data, 'Goal');

        $currentTerm = $Team->EvaluateTerm->getTermData(EvaluateTerm::TYPE_CURRENT);

        $data['start_date'] = $TimeExHelper->dateFormat($data['start_date'], $currentTerm['timezone']);
        $data['end_date'] = $TimeExHelper->dateFormat($data['end_date'], $currentTerm['timezone']);

        return $this->extend($data, $extends);
    }

    function extend($data, $extends) {
        if (empty($data) || empty($extends)) {
            return $data;
        }
        $Goal = ClassRegistry::init("Goal");
        if (in_array(self::EXTEND_GOAL_LABELS, $extends)) {
            $data['goal_labels'] = Hash::extract($Goal->GoalLabel->findByGoalId($data['id']), '{n}.Label');
        }

        if (in_array(self::EXTEND_TOP_KEY_RESULT, $extends)) {
            $data['key_result'] = Hash::extract($Goal->KeyResult->getTkr($data['id']), 'KeyResult');
        }
        return $data;
    }



    /**
     * TODO:モデルで行う処理では無いので将来的に他の適切な場所に移行すること
     * 画像のurlを取得
     * - パラメタ $photoStyles は取得するサムネイルの名前を指定。Uploadビヘイビアで設定済みのものが有効。指定しない場合はすべて取得する.
     * - パラメタ $photoStylesで存在しないスタイルを指定された場合はスキップ。
     *
     * @param array  $data
     * @param string $modelName
     * @param array  $photoStyles
     *
     * @return array
     */
    function attachImgUrl($data, $modelName, $photoStyles = [])
    {
        $Goal = ClassRegistry::init("Goal");
        $UploadHelper = new UploadHelper(new View());
        $defaultStyles = array_keys($Goal->actsAs['Upload']['photo']['styles']);
        if (empty($photoStyles)) {
            $photoStyles = $defaultStyles;
            $photoStyles[] = 'original';
        }
        foreach ($photoStyles as $style) {
            if ($style != 'original' && !in_array($style, $defaultStyles)) {
                continue;
            }
            $data["{$style}_img_url"] = $UploadHelper->uploadUrl($data,
                "$modelName.photo",
                ['style' => $style]);
        }
        return $data;
    }
}

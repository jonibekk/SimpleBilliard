<?php
App::import('Service', 'AppService');
App::import('Service', 'UserService');
App::uses('Circle', 'Model');
App::uses('CirclePin', 'Model');
App::uses('CircleMember', 'Model');
App::uses('CirclesController', 'Controller');
App::uses('AppUtil', 'Util');

/**
 * Class CircleMemberService
 */
class CirclePinService extends AppService
{
    /**
     * 自分が所属しているサークルのAdminか判定する
     * @return bool
     */
    public function isUserCircleAdmin(int $userId, int $circleId): bool {
        $isAdmin = false;
        try{
            $isAdmin = ClassRegistry::init('CircleMember')->isAdmin($userId, $circleId);
        }catch (RuntimeException $e) {
            $this->log(sprintf("[%s]%s", __METHOD__, $e->getMessage()));
            $this->log($e->getTraceAsString());
            return false;
        }
        $html = "";

        if($isAdmin){
            $circleEdit = ClassRegistry::init('Circle')->Html->url([
                'controller' => 'circles',
                'action'     => 'ajax_get_edit_modal',
                'circle_id'  => intval($circleId),
            ]);
            $html = "<a href='#'
                       data-url='" . $circleEdit . "'
                       class='a-black-link'>
                       <i class='fa-pull-right fas fa-ellipsis-h fa-lg'></i>
                    </a>";
        }

        return $html;
    }

    /**
     * 自分が所属しているサークルを抜けるときに更新処理を行うメソッド
     * @return bool
     */
    public function deleteCircleOrder(int $userId, int $teamId, int $circleId): bool
    {
        try{
            ClassRegistry::init('CirclePin')->deleteId($userId, $teamId, $orders);
        } catch (RuntimeException $e) {
            $this->log(sprintf("[%s]%s", __METHOD__, $e->getMessage()));
            $this->log($e->getTraceAsString());
            return false;
        }

        return true;
    }

    /**
     * 自分が所属しているサークルのソート順をDBへセットする
     * @return bool
     */
    public function setCircleOrders(int $userId, int $teamId, string $orders): bool
    {
        try{
            ClassRegistry::init('CirclePin')->insertUpdate($userId, $teamId, $orders);
        } catch (RuntimeException $e) {
            $this->log(sprintf("[%s]%s", __METHOD__, $e->getMessage()));
            $this->log($e->getTraceAsString());
            return false;
        }

        return true;
    }
    /**
     * 自分が所属しているサークルをソートして返す
     * @return array
     */
    public function getMyCircleSortedList(int $userId, int $teamId): array
    {
        //
        $Circle = ClassRegistry::init('Circle');
        $CircleMember = ClassRegistry::init('CircleMember');
        $CirclePin = ClassRegistry::init('CirclePin');
        $Upload = new UploadHelper(new View());

        $memberOptions = [
            'conditions' => [
                'user_id' => $userId,
            ],
            'fields'     => [
                'circle_id',
                'admin_flg',
                'unread_count',
            ],
        ];
        $memberResults = $CircleMember->find('all', $memberOptions);

        $memberArray = [];
        if(!empty($memberResults)){
            $memberArray = array_map(function ($a) { 
                return [
                    'circle_id' => $a['CircleMember']['circle_id'], 
                    'admin_flg' => $a['CircleMember']['admin_flg'],
                    'unread_count' => $a['CircleMember']['unread_count']
                ]; 
            }, $memberResults);
            //$memberArray = Hash::combine($memberResults, '{n}.circle_id', '{n}');
            //TODO:by hash command or set or classic set
        }
GoalousLog::error("memberResults",$memberResults);
        $pinOptions = [
            'conditions' => [
                'user_id' => $userId,
                'team_id' => $teamId,
            ],
            'fields'     => [
                'circle_orders',
            ],
        ];
        $pinResults = $CirclePin->find('list', $pinOptions);

        $pinOrderString = '';
        if(!empty($pinResults)){
            $pinOrderString = reset($pinResults);
        }
        $pinOrders = explode(',', $pinOrderString);

        $idOptions = [
            'conditions' => [
                'user_id' => $userId,
                'team_id' => $teamId,
            ],
            'fields'     => [
                'circle_id',
            ],
        ];        
        $circleIds = $CircleMember->find('list', $idOptions);

        $circleOptions = [
            'conditions' => [
                'id' => $circleIds,
                'team_id' => $teamId,
            ],
            'fields'     => [
                'id',
                'name',
                'photo_file_name',
                'public_flg',
                'team_all_flg',
                'created',
                'modified',
            ],
        ];
        $circles = $Circle->find('all', $circleOptions);
        $circles = Hash::combine($circles, '{n}.Circle.id', '{n}.Circle');


        $defaultCircle;
        $regularaCircles = [];
        foreach ($circles as &$circle) {
            $key = $circle['id'];
            $keyMember = array_search($key, array_column($memberArray, 'circle_id'));
            $circle['admin_flg'] = $memberArray[$keyMember]['admin_flg'];
            $circle['unread_count'] = $memberArray[$keyMember]['unread_count'];
            if($circle['unread_count'] == null){
                $circle['unread_count'] = 100;
            }
            // if(in_array($key, $memberArray)){
            //     $index = array_search($key, $memberArray);
            //     $circle['admin_flg'] = $memberArray[$index]['admin_flg'];
            //     $circle['unread_count'] = $memberArray[$index]['unread_count'];
            // } else {
            //     $circle['admin_flg'] = true;//TODO:debug
            //     $circle['unread_count'] = 0;
            // }

            if(in_array($key, $pinOrders)){
                $circle['order'] = array_search($key, $pinOrders) + 1;
            } else {
                $circle['order'] = null;
            }

            $circle['image'] = $Upload->uploadUrl($circle, 'Circle.photo', ['style' => 'small']);

            if($circle['team_all_flg']) {
                $defaultCircle = $circle;
            } else {
                $regularaCircles[] = $circle;
            }
        }

        $regularaCircles = Hash::sort($regularaCircles, '{n}.modified', 'desc', 'numeric');
        $regularaCircles = Hash::sort($regularaCircles, '{n}.order', 'asc', 'numeric');
        //TODO:For Debugging SQL Queries
        //debug(ClassRegistry::init('CirclePin')->getDataSource()->getLog(false, false));
        $returnArray['regular_circle'] = $regularaCircles;
        $returnArray['default_circle'] = $defaultCircle;
        
GoalousLog::error("CIRCLES",$regularaCircles);
        return $returnArray;
    }
}

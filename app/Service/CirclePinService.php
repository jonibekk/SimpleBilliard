<?php
App::import('Service', 'AppService');
App::import('Service', 'UserService');
App::uses('Circle', 'Model');
App::uses('CirclePin', 'Model');
App::uses('CircleMember', 'Model');
App::uses('AppUtil', 'Util');

/**
 * Class SavedPostService
 */
class CirclePinService extends AppService
{
    function getDefaultCircle(){
        $Circle = ClassRegistry::init('Circle');
        return $Circle->getCirclesDefault();
    }

    /**
     * Find pinned circles.
     *
     * @return array
     */
    function getPinned(): array
    {
        //Retreive User's Circle List
        $CirclePin = ClassRegistry::init('CirclePin');
        $pins = $CirclePin->getPinnedCircles();

        //Retrieve Actual Circle Data
        $Circle = ClassRegistry::init('Circle');
        $CircleMember = ClassRegistry::init('CircleMember');
        $circles = $Circle->getCirclesByIds($CircleMember->getMyCircleList());
        //$circles = Hash::combine($circles, '{n}.Circle.id', '{n}.Circle');


        $pinned = array();
        $unpinned = array();

        foreach ($pins as $circleId => $pinOrder) {
            if(array_key_exists($circleId, $circles)){
                $target = $circles[$circleId];
                $target['Circle']['pin_order'] = $pinOrder['CirclePin']['pin_order'];
                $pinned[] = $target;
            }
        }
        foreach ($circles as $key => $value) {
            if(!array_key_exists($key, $pinned)){
                $unpinned[] = $value;
            }
        }
        // $pinned = Hash::combine($pinned, '{n}.Circle.id', '{n}.Circle');
        // $unpinned = Hash::combine($unpinned, '{n}.Circle.id', '{n}.Circle');
        usort($pinned, function ($item1, $item2) {
            return $item1['pin_order'] <=> $item2['pin_order'];
        });
        usort($pinned, function($a, $b) {
            return $a['order'] <=> $b['order'];
        });
        $results['pinned'] = $pinned;
        $results['unpinned'] = $unpinned;
$this->log($pinned);
        return $results;
    }

    /**
     * build circle pin data
     *
     * @param  int     $userId
     * @param  int     $teamId
     * @param  array   $pinOrderData
     *
     * @return array
     */
    function buildJoinData(int $userId, int $teamId, array $pinOrderData): array
    {
        /** @var CirclePin $CirclePin */
        $CirclePin = ClassRegistry::init('CirclePin');
        /** @var CircleMember $CircleMember */
        $CircleMember = ClassRegistry::init('CircleMember');

        $saveData = [
            'user_id'               => $userId,
            'team_id'               => $CircleMember->current_team_id,
            'circle_id'             => $pinOrderData['circle_id'],
            'pin_order'             => $pinOrderData['pin_order'],
            'del_flg'               => false,
        ];
        return $saveData;
    }

    /**
     * BulkInsert circle pin
     *
     * @param int    $userId
     * @param int    $teamId
     * @param array  $pinOrderDatas
     *
     * @return bool
     */
    function bulkInsert(int $userId, int $teamId, array $pinOrderDatas): bool
    {
        /** @var CirclePin $CirclePin */
        $CirclePin = ClassRegistry::init('CirclePin');
        $conditions = [
            'CirclePin.user_id' => $userId,
        ];
        try {
            $CirclePin->begin();
            if (!$CirclePin->deleteAll($conditions)) {
                throw new Exception(sprintf("Failed to delete orders of circle pin."));
            }
            // build save data
            $saveData = [];
            foreach ($pinOrderDatas as $pinOrderData) {
                $saveData[] = $this->buildJoinData($userId, $teamId, $pinOrderData);
            }
            if(!empty($saveData)){
                // bulk save
                if (!$CirclePin->bulkInsert($saveData)) {
                    throw new Exception(sprintf("Failed to add orders to circle pin. data:%s", var_export($saveData, true)));
                }
            }
        } catch (Exception $e) {
            $this->log(sprintf("[%s]%s", __METHOD__, $e->getMessage()));
            $this->log($e->getTraceAsString());
            $CirclePin->rollback();
            return false;
        }

        $CirclePin->commit();
        return true;
    }

    /**
     * Delete all circle pins belonging to a user
     *
     * @param int    $userId
     *
     * @return bool
     */
    function delete(int $userId): bool{
        /** @var CirclePin $CirclePin */
        $CirclePin = ClassRegistry::init('CirclePin');

        try {
            $CirclePin->begin();

            // delete
            if (!$CirclePin->delete($userId)) {
                throw new Exception("Failed to delete circle pins.");
            }
        } catch (Exception $e) {
            $this->log(sprintf("[%s]%s", __METHOD__, $e->getMessage()));
            $this->log($e->getTraceAsString());
            $CirclePin->rollback();
            return false;
        }

        $CirclePin->commit();
        return true;
    }
}

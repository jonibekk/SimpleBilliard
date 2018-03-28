<?php
App::import('Service', 'AppService');
App::import('Service', 'UserService');
App::uses('Circle', 'Model');
App::uses('CirclePin', 'Model');
App::uses('CircleMember', 'Model');
App::uses('CirclesController', 'Controller');
App::uses('AppUtil', 'Util');

/**
 * Class CirclePinService
 */
class CirclePinService extends AppService
{
    /**
     * Circle Pins Order Validation
     *
     * @param  string $pinOrder
     *
     * @return true|CakeResponse
     */
    function validateApprovalPinOrder($pinOrder)
    {
        /** @var CirclePin $CirclePin */
        $CirclePin = ClassRegistry::init("CirclePin");
        $validation = [];

        $validation = $CirclePin->validates();

        if($validation !== true){
            $validation = $this->_validationExtract($CirclePin->validationErrors);
        }

        if(isset($validation)){
            return $validation;
        }

        return true;
    }

    /**
     * Deletes specified circleId from circle pin order information
     * example: 3,4,5 => ,3,4,5, => (,4,) => ,3,5, => 3,5 
     * @param $userId
     * @param $teamId
     * @param $circleId
     *
     * @return bool
     */
    public function deleteCircleId(int $userId, int $teamId, string $circleId): bool 
    {
        /** @var CirclePin $CirclePin */
        $CirclePin = ClassRegistry::init('CirclePin');
        $options = [
            'user_id' => $userId,
            'team_id' => $teamId,
        ];
   
        try {    
            $row = $CirclePin->getUnique($userId, $teamId);

            if(empty($row)){
                return true;
            }

            $orders = ',' . $row['circle_orders'] . ',';
            $find = ',' . $circleId . ',';

            if(strpos($orders, $find) !== false){
                $orders = str_replace($find, ',', $orders);
                $row['circle_orders'] = substr($orders, 1, -1);
                $options['id'] = $row['id'];

                if(!$CirclePin->save($row, $options)) {
                    throw new Exception("Error Processing Delete Request", 1);             
                }
            }   
        } catch (Exception $e) {
            $this->log(sprintf("[%s]%s", __METHOD__, $e->getMessage()));
            $this->log($e->getTraceAsString());
            return false;
        }

        return true;
    }

    /**
     * Save Circle Pin Order Information
     *
     * @param $userId
     * @param $teamId
     * @param $pinOrders
     *
     * @return bool
     */
    public function setCircleOrders(int $userId, int $teamId, string $pinOrders): bool
    {
        /** @var CirclePin $CirclePin */
        $CirclePin = ClassRegistry::init('CirclePin');
        $db = $CirclePin->getDataSource();

        $options = [
            'user_id' => $userId,
            'team_id' => $teamId,
        ];

        $data = [
            'CirclePin' => [
                'user_id' => $userId,
                'team_id' => $teamId,
                'circle_orders' => $db->value($pinOrders, 'string'),
                'del_flg' => false,
            ],
        ];
        $row = $CirclePin->getUnique($userId, $teamId);
            if(empty($row)){
                $CirclePin->create($data);
                if(!$CirclePin->save($data)){
                    throw new Exception("Error Processing Insert Request", 1);
                }
            } else {
                $row['circle_orders'] = $data['CirclePin']['circle_orders'];
                $options['id'] = $row['id'];
                if(!$CirclePin->save($row, $options)) {
                    throw new Exception("Error Processing Update Request", 1);             
                }
            }
        try{
            
        } catch (RuntimeException $e) {
            $this->log(sprintf("[%s]%s", __METHOD__, $e->getMessage()));
            $this->log($e->getTraceAsString());
            return false;
        }

        return true;
    }
    /**
     * Get Relevant Ordered Circles Data
     *
     * @param $userId
     * @param $teamId
     *
     * @return array
     */
    public function getMyCircleSortedList(int $userId, int $teamId): array
    {
        /** @var CirclePin $CirclePin */
        $CirclePin = ClassRegistry::init('CirclePin');
        $Upload = new UploadHelper(new View());

        $pinOrders = [];
        $circles = $CirclePin->getJoinedCircleData($userId, $teamId);
        $pinOrderInformation = $CirclePin->getPinData($userId, $teamId);

        if($pinOrderInformation !== ''){
            $pinOrders = explode(',', $pinOrderInformation);
        }

        foreach ($circles as &$circle) {
            $circle['Data'] = array_merge($circle['Circle'],$circle['CircleMember']);
            $circle['Data']['image'] = $Upload->uploadUrl($circle, 'Circle.photo', ['style' => 'small']);
            $circle = $circle['Data'];
            $circle['order'] = null;
            unset($circle['Circle']);
            unset($circle['CircleMember']);
            unset($circle['CirclePin']);
            unset($circle['Data']);
        }

        
        $counter = 0;
        $circleIds = array_column($circles, 'id');
        foreach ($pinOrders as $key => $circleId) {
            $arrayKey = array_search($circleId, $circleIds);
            if($arrayKey !== false){
                $circles[$arrayKey]['order'] = $counter;
                $counter++;
            }
        }

        $defaultCircle = [];
        $defaultCircleKey = array_search(true, array_column($circles, 'team_all_flg'));
        if($defaultCircleKey !== false){
            $defaultCircle = $circles[$defaultCircleKey];
            unset($circles[$defaultCircleKey]);
        }

        // $circles = Hash::sort($circles, '{n}.modified', 'desc', 'numeric');
        $unsortedCircles = array_filter($circles, function($value, $key){
           return !isset($value['order']);
        }, ARRAY_FILTER_USE_BOTH);
        $sortedCircles = array_filter($circles, function($value, $key){
           return isset($value['order']);
        }, ARRAY_FILTER_USE_BOTH);
        $sortedCircles = Hash::sort($sortedCircles, '{n}.order', 'asc', 'numeric');
        $orderedCircles = array_merge($sortedCircles, $unsortedCircles);

        $returnArray['regular_circle'] = $orderedCircles;
        $returnArray['default_circle'] = $defaultCircle;
        return $returnArray;
    }
}

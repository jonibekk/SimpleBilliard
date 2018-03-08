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
    function searchForCircleId($id, $array) {
       foreach ($array as $key => $val) {
           if ($val['id'] === $id) {
               return $key;
           }
       }
       return null;
    }

    function getDefaultCircle(){
        $Circle = ClassRegistry::init('Circle');
        return $Circle->convertPhotoUrls($Circle->getCirclesDefault());
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

        $results = array();
        foreach ($circles as &$circle) {
            $target = $this->searchForCircleId($circle['Circle']['id'], $pins);
            if(!isset($target)){
                $circle['Circle']['id'] = null;
            }else{
                $circle['pin_order'] = $target['pin_order'];
            }
        }

        return $circles;
    }

    /**
     * Find pinned circles.
     *
     * @return array
     */
    function getUnpinned(): array
    {
        //Retreive User's Circle List
        $CirclePin = ClassRegistry::init('CirclePin');
        $pins = $CirclePin->getUnpinnedCircles();

        //Retrieve Actual Circle Data
        $Circle = ClassRegistry::init('Circle');
        $CircleMember = ClassRegistry::init('CircleMember');
        $circles = $Circle->getCirclesByIds($CircleMember->getMyCircleList());

        $results = array();
        foreach ($circles as &$circle) {
            $target = $this->searchForCircleId($circle['Circle']['id'], $pins);
            $circle['pin_order'] = $target['pin_order'];
        }

        return $circles;
    }
}

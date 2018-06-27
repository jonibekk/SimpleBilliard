<?php
/**
 * Created by PhpStorm.
 * User: StephenRaharja
 * Date: 2018/06/28
 * Time: 12:07
 */

class CircleListPagingTest
{
    public $fixtures = [
        'app.team',
        'app.user',
        'app.circle',
        'app.circle_member',
        'app.experiment',
    ];

    public function test_getCircleList_success()
    {
        /** @var CircleListPagingService $CircleListPagingService */
        $CircleListPagingService = ClassRegistry::init('CircleListPagingService');


    }

    public function test_getCircleListWithCursor_success()
    {
    }

}
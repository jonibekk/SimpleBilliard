<?php
App::uses('GoalousTestCase', 'Test');
App::uses('StringUtil', 'Util');

class StringUtilTest extends GoalousTestCase
{
    public function test_splitAndMerge_success()
    {
        $length = 1500;

        $originalString = $this->getLongArticle();

        $segmentedString = StringUtil::splitStringToSegments($originalString, $length);

        foreach ($segmentedString as $string) {
            $this->assertTrue(mb_strlen($string, 'UTF-8') <= $length);
        }

        $mergedString = StringUtil::mergeSegmentsToString($segmentedString);

        $this->assertEquals($mergedString, $originalString);
    }


}
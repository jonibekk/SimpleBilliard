<?php
App::uses('GoalousTestCase', 'Test');
App::uses('PaymentUtil', 'Util');

/**
 * PaymentUtil Test Case
 */
class PaymentUtilTest extends GoalousTestCase
{

    function test_parsePlanCode()
    {
        $code = "";
        $errMsgTpl = "Failed to parse price plan code";
        try {
            $res = PaymentUtil::parsePlanCode($code);
        } catch (Exception $e) {
            $res = $e->getMessage();
            echo $res;
        }
        $this->assertTrue(strpos($res, $errMsgTpl) !== true);

        $code = "aaa";
        try {
            $res = PaymentUtil::parsePlanCode($code);
        } catch (Exception $e) {
            $res = $e->getMessage();
            echo $res;
        }
        $this->assertTrue(strpos($res, $errMsgTpl) !== true);

        $code = "aaa-1-aaa";
        try {
            $res = PaymentUtil::parsePlanCode($code);
        } catch (Exception $e) {
            $res = $e->getMessage();
            echo $res;
        }
        $this->assertTrue(strpos($res, $errMsgTpl) !== true);

        $code = "aaa-";
        try {
            $res = PaymentUtil::parsePlanCode($code);
        } catch (Exception $e) {
            $res = $e->getMessage();
            echo $res;
        }
        $this->assertTrue(strpos($res, $errMsgTpl) !== true);

        $code = "aaa-2";
        try {
            $res = PaymentUtil::parsePlanCode($code);
        } catch (Exception $e) {
            $res = $e->getMessage();
            echo $res;
        }
        $this->assertTrue(strpos($res, $errMsgTpl) !== true);

        $code = "1-a";
        try {
            $res = PaymentUtil::parsePlanCode($code);
        } catch (Exception $e) {
            $res = $e->getMessage();
            echo $res;
        }
        $this->assertTrue(strpos($res, $errMsgTpl) !== true);

        $code = "1-a2";
        try {
            $res = PaymentUtil::parsePlanCode($code);
        } catch (Exception $e) {
            $res = $e->getMessage();
            echo $res;
        }
        $this->assertTrue(strpos($res, $errMsgTpl) !== true);

        $code = "1-a2";
        try {
            $res = PaymentUtil::parsePlanCode($code);
        } catch (Exception $e) {
            $res = $e->getMessage();
            echo $res;
        }
        $this->assertTrue(strpos($res, $errMsgTpl) !== true);


        $code = "1-2";
        try {
            $res = PaymentUtil::parsePlanCode($code);
        } catch (Exception $e) {
            $res = $e->getMessage();
            echo $res;
        }
        $this->assertEquals($res['group_id'], 1);
        $this->assertEquals($res['detail_no'], 2);

        $code = "9999999-1000000";
        try {
            $res = PaymentUtil::parsePlanCode($code);
        } catch (Exception $e) {
            $res = $e->getMessage();
            echo $res;
        }
        $this->assertEquals($res['group_id'], 9999999);
        $this->assertEquals($res['detail_no'], 1000000);
    }
}

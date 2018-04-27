<?php

/**
 * Summarize goalous test case directories.
 */
class BaseTest extends PHPUnit_Framework_TestSuite
{
    protected static $testDirectories = [
        'CIParallelTests1' => [
            APP_TEST_CASES . DS . 'View' . DS . 'Helper',
            APP_TEST_CASES . DS . 'Console',
            APP_TEST_CASES . DS . 'Lib' . DS . 'Util',
            APP_TEST_CASES . DS . 'Model',
            APP_TEST_CASES . DS . 'Service' . DS . 'Payment1',
        ],
        'CIParallelTests2' => [
            APP_TEST_CASES . DS . 'Service',
            APP_TEST_CASES . DS . 'Service' . DS . 'Payment2',
            APP_TEST_CASES . DS . 'Service' . DS . 'Api',
            APP_TEST_CASES . DS . 'Validator'
        ],
    ];
}

<?php

/**
 * Summarize goalous test case directories.
 */
class BaseTest extends PHPUnit_Framework_TestSuite
{
    protected static $testDirectories = [
        'CIParallelTests1' => [
            'directory' => [
                APP_TEST_CASES . DS . 'View' . DS . 'Helper',
                APP_TEST_CASES . DS . 'Console',
                APP_TEST_CASES . DS . 'Model',
                APP_TEST_CASES . DS . 'Service' . DS . 'Payment1',
            ],
            'directoryRecursive' => [
                APP_TEST_CASES . DS . 'Lib',
            ],
        ],
        'CIParallelTests2' => [
            'directory' => [
                APP_TEST_CASES . DS . 'Service',
                APP_TEST_CASES . DS . 'Service' . DS . 'Payment2',
                APP_TEST_CASES . DS . 'Service' . DS . 'Api',
                APP_TEST_CASES . DS . 'Validator',
                APP_TEST_CASES . DS . 'Validator' . DS . 'Rule',
            ],
            'directoryRecursive' => [
            ],
        ],
    ];
}

<?php
/**
 * AllTests file
 *
 * PHP 5
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright 2005-2012, Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 2005-2012, Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       Cake.Test.Case
 * @since         CakePHP(tm) v 2.0
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

/**
 * AllTests class
 *
 * This test group will run all tests.
 *
 * @package       Cake.Test.Case
 */
class AllTests extends PHPUnit_Framework_TestSuite
{

    /**
     * Suite define the tests for this suite
     *
     * @return CakeTestSuite
     */
    public static function suite()
    {
        $suite = new CakeTestSuite('All Application Test');
        $suite->addTestDirectory(APP_TEST_CASES . DS . 'Controller');
        $suite->addTestDirectory(APP_TEST_CASES . DS . 'Model');
        return $suite;
    }

}

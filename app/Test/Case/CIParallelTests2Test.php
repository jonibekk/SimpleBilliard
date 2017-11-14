<?php
require_once "BaseTest.php";
/**
 * AllTests file
 * PHP 5
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright 2005-2012, Cake Software Foundation, Inc. (http://cakefoundation.org)
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
 * CIParallelTests2 class
 * This test group will run 2nd tests for Travis CI parallel processes.
 *
 * @package       Cake.Test.Case
 */
class CIParallelTests2 extends BaseTest
{

    /**
     * Suite define the tests for this suite
     *
     * @return CakeTestSuite
     */
    public static function suite()
    {
        ini_set('memory_limit', '2G');
        $suite = new CakeTestSuite('Travis CI Parallel2 Test');
        $testDirectories = static::$testDirectories[__CLASS__];
        foreach ($testDirectories as $dir) {
            $suite->addTestDirectory($dir);
        }
        return $suite;
    }

}

<?php
/**
 * CakePHP(tm) Tests <http://book.cakephp.org/2.0/en/development/testing.html>
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://book.cakephp.org/2.0/en/development/testing.html CakePHP(tm) Tests
 * @package       Cake.TestSuite.Fixture
 * @since         CakePHP(tm) v 1.2.0.4667
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */

/**
 * CakeTestFixture is responsible for building and destroying tables to be used
 * during testing.
 *
 * @package       Cake.TestSuite.Fixture
 */
class CakeTestFixtureEx extends CakeTestFixture
{

    /**
     * Run before all tests execute, should return SQL statement to create table for this fixture could be executed successfully.
     *
     * @param DboSource $db An instance of the database object used to create the fixture table
     *
     * @return bool True on success, false on failure
     */
    public function create($db)
    {
        if (!isset($this->fields) || empty($this->fields)) {
            return false;
        }
        //fix field type of primary 
        if ($db->config['datasource'] == 'Database/Mysql') {
            $this->fields['id']['type'] = 'integer';
        }
        elseif ($db->config['datasource'] == 'Database/Sqlite') {
            $this->fields['id']['type'] = 'primary_key';
        }
        //fix sqlite setting
        if ($db->config['datasource'] == 'Database/Sqlite') {
            foreach ($this->fields as $field_name => $attr) {
                if (isset($attr['null']) && $attr['null'] == false &&
                    array_key_exists('default', $attr) && is_null($attr['default'])
                ) {
                    unset($this->fields[$field_name]['null']);
                }
            }
        }

        if (empty($this->fields['tableParameters']['engine'])) {
            $canUseMemory = $this->canUseMemory;
            foreach ($this->fields as $args) {

                if (is_string($args)) {
                    $type = $args;
                }
                elseif (!empty($args['type'])) {
                    $type = $args['type'];
                }
                else {
                    continue;
                }

                if (in_array($type, array('blob', 'text', 'binary'))) {
                    $canUseMemory = false;
                    break;
                }
            }

            if ($canUseMemory) {
                $this->fields['tableParameters']['engine'] = 'MEMORY';
            }
        }
        $this->Schema->build(array($this->table => $this->fields));
        try {
            $db->execute($db->createSchema($this->Schema), array('log' => false));
            $this->created[] = $db->configKeyName;
        } catch (Exception $e) {
            $msg = __d(
                'cake_dev',
                'Fixture creation for "%s" failed "%s"',
                $this->table,
                $e->getMessage()
            );
            CakeLog::error($msg);
            trigger_error($msg, E_USER_WARNING);
            return false;
        }
        return true;
    }
}

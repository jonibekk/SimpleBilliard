<?php
App::import('Vendor', 'iRedis/iredis');
App::uses('DatabaseSession', 'Model/Datasource/Session');
/**
 * Redis Session Store for CakePHP 2
 *
 * @version   0.1
 * @author    Kjell Bublitz <m3nt0r.de@gmail.com>
 * @license   MIT License
 * @copyright 2011, Kjell Bublitz
 * @package   redis_session
 *            Permission is hereby granted, free of charge, to any person obtaining a copy
 *            of this software and associated documentation files (the "Software"), to deal
 *            in the Software without restriction, including without limitation the rights
 *            to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 *            copies of the Software, and to permit persons to whom the Software is
 *            furnished to do so, subject to the following conditions:
 *            The above copyright notice and this permission notice shall be included in
 *            all copies or substantial portions of the Software.
 *            THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 *            IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 *            FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 *            AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 *            LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 *            OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 *            THE SOFTWARE.
 */

/**
 * Redis Session Store Class
 */
class RedisSession extends DatabaseSession implements CakeSessionHandlerInterface
{

    /**
     * @var Redis $store
     */
    private static $store;
    private static $timeout;
    private static $key;

    public function __construct()
    {
        parent::__construct();
        $timeout = Configure::read('Session.timeout');
        if (empty($timeout)) {
            $timeout = 60 * 24 * 90;
        }
        self::$timeout = $timeout;
        self::$key = Configure::read('Session.handler.key') ? Configure::read('Session.handler.key') : null;
    }

    /**
     * open
     * connect to Redis
     * authorize
     * select database
     */
    public function open()
    {
        App::uses('ConnectionManager', 'Model');
        self::$store = ConnectionManager::getDataSource('redis');
    }

    /**
     * close
     * disconnect from Redis
     *
     * @return type
     */
    public function close()
    {
        self::$store->close();
        return true;
    }

    /**
     * read
     *
     * @param type $id
     *
     * @return type
     * - Return whatever is stored in key
     */
    public function read($id)
    {
        return self::$store->get(self::$key . $id);
    }

    /**
     * write
     *
     * @param type $id
     * @param type $data
     *
     * @return type
     * - SETEX data with timeout calculated in open()
     */
    public function write($id, $data)
    {
        self::$store->setex(self::$key . $id, self::$timeout, $data);
        return true;
    }

    /**
     * destroy
     *
     * @param type $id
     *
     * @return type
     * - DEL the key from store
     */
    public function destroy($id)
    {
        self::$store->del(self::$key . $id);
        return true;
    }

    /**
     * gc
     *
     * @param type $expires
     *
     * @return type
     * not needed as SETEX automatically removes itself after timeout
     */
    public function gc($expires = null)
    {
        return true;
    }
}

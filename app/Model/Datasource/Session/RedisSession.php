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
    private $store;
    private $timeout;
    private $key;

    public function __construct()
    {
        parent::__construct();
        $timeout = Configure::read('Session.timeout');
        if (empty($timeout)) {
            $timeout = 60 * 24 * 90;
        } else {
            $timeout *= 60;
        }
        $this->timeout = $timeout;
        $this->key = Configure::read('Session.handler.key') ? Configure::read('Session.handler.key') : null;
        App::uses('ConnectionManager', 'Model');
        $this->store = ConnectionManager::getDataSource('redis');
    }

    /**
     * open
     */
    public function open()
    {
        if (!$this->store) {
            App::uses('ConnectionManager', 'Model');
            $this->store = ConnectionManager::getDataSource('redis');
        }
        return true;
    }

    /**
     * close
     * disconnect from Redis
     *
     * @return bool
     */
    public function close()
    {
        // Start session -> Close session -> Start session という遷移をすると
        // エラーが発生するので、ここでは RedisSource::close() を呼ばない。
        // RedisSource::__destruct() の処理に任せる。
        //$this->store->close();

        return true;
    }

    /**
     * read
     *
     * @param  $id
     *
     * @return string
     * - Return whatever is stored in key
     */
    public function read($id)
    {
        if (!$this->store) {
            App::uses('ConnectionManager', 'Model');
            $this->store = ConnectionManager::getDataSource('redis');
        }
        $sessionStr = $this->store->get($this->key . $id);
        if(is_string($sessionStr)){
            return $sessionStr;
        }
        return '';
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
        $this->store->setex($this->key . $id, $this->timeout, $data);
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
        $this->store->del($this->key . $id);
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

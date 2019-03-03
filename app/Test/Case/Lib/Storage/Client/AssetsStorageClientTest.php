<?php
App::uses('GoalousTestCase', 'Test');
App::import('Lib/Storage', 'UploadedFile');
App::import('Lib/Storage/Client', 'AssetsStorageClient');

/**
 * Created by PhpStorm.
 * User: stephen
 * Date: 19/02/27
 * Time: 20:00
 */
class AssetsStorageClientTest extends GoalousTestCase
{

    public function test_getKeyNameUTF8_success()
    {
        $method = new ReflectionMethod('AssetsStorageClient', 'createFileKey');

        $method->setAccessible(true);

        $result = $method->invoke(new AssetsStorageClient('AttachedFile', 1), 'たたたた.jpg', '_original', 'jpg');

        $this->assertNotEmpty($result);

        $result = $method->invoke(new AssetsStorageClient('AttachedFile', 1), '\u8923\u9032.jpg', '_original', 'jpg');

        $this->assertNotEmpty($result);
    }
}
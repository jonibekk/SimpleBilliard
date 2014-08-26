<?php
App::uses('Image', 'Model');

/**
 * Image Test Case
 *
 * @property mixed Image
 */
class ImageTest extends CakeTestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = array(
        'app.image',
        'app.user', 'app.notify_setting',
        'app.badge',
        'app.team',
        'app.post',
        'app.images_post'
    );

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->Image = ClassRegistry::init('Image');
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Image);

        parent::tearDown();
    }

    //ダミーテスト
    function testDummy()
    {
    }

}

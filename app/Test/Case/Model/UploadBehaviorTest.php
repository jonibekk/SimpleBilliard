<?php App::uses('GoalousTestCase', 'Test');
App::uses('UploadBehavior', 'Model/Behavior');

/**
 * UploadBehavior Test Case
 *
 * @property UploadBehavior $UploadBehavior
 */
class UploadBehaviorTest extends GoalousTestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = array(
    );

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->UploadBehavior = ClassRegistry::init('UploadBehavior');
        mkdir('Test/OutputImages', 0755, true);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->ActionResultFile);
        array_map('unlink', glob('Test/OutputImages/*.*'));
        rmdir('Test/OutputImages');
        parent::tearDown();
    }

    public function test_getOutputHandler(){
        $res = $this->UploadBehavior->getOutputHandler('gif');
        $this->AssertEquals($res, 'imagegif');
        $res = $this->UploadBehavior->getOutputHandler('jpg');
        $this->AssertEquals($res, 'imagejpeg');
        $res = $this->UploadBehavior->getOutputHandler('jpeg');
        $this->AssertEquals($res, 'imagejpeg');
        $res = $this->UploadBehavior->getOutputHandler('png');
        $this->AssertEquals($res, 'imagejpeg');
        $res = $this->UploadBehavior->getOutputHandler('zzz');
        $this->AssertFalse($res);
    }

    public function test_getCreateHandler(){
        $res = $this->UploadBehavior->getCreateHandler('gif');
        $this->AssertEquals($res, 'imagecreatefromgif');
        $res = $this->UploadBehavior->getCreateHandler('jpg');
        $this->AssertEquals($res, 'imagecreatefromjpeg');
        $res = $this->UploadBehavior->getCreateHandler('jpeg');
        $this->AssertEquals($res, 'imagecreatefromjpeg');
        $res = $this->UploadBehavior->getCreateHandler('png');
        $this->AssertEquals($res, 'imagecreatefrompng');
        $res = $this->UploadBehavior->getCreateHandler('zzz');
        $this->AssertFalse($res);
    }

    public function test_saveRotatedFile(){
        $res = $this->UploadBehavior->saveRotatedFile("Test/Images/Landscape_1.jpg", "Test/OutputImages/Landscape_1.jpg");
        $this->AssertTrue($res);
        $res = $this->UploadBehavior->saveRotatedFile("Test/Images/Landscape_2.jpg", "Test/OutputImages/Landscape_2.jpg");
        $this->AssertTrue($res);
        $res = $this->UploadBehavior->saveRotatedFile("Test/Images/Landscape_3.jpg", "Test/OutputImages/Landscape_3.jpg");
        $this->AssertTrue($res);
        $res = $this->UploadBehavior->saveRotatedFile("Test/Images/Landscape_4.jpg", "Test/OutputImages/Landscape_4.jpg");
        $this->AssertTrue($res);
        $res = $this->UploadBehavior->saveRotatedFile("Test/Images/Landscape_5.jpg", "Test/OutputImages/Landscape_5.jpg");
        $this->AssertTrue($res);
        $res = $this->UploadBehavior->saveRotatedFile("Test/Images/Landscape_6.jpg", "Test/OutputImages/Landscape_6.jpg");
        $this->AssertTrue($res);
        $res = $this->UploadBehavior->saveRotatedFile("Test/Images/Landscape_7.jpg", "Test/OutputImages/Landscape_7.jpg");
        $this->AssertTrue($res);
        $res = $this->UploadBehavior->saveRotatedFile("Test/Images/Landscape_8.jpg", "Test/OutputImages/Landscape_8.jpg");
        $this->AssertTrue($res);
        $res = $this->UploadBehavior->saveRotatedFile("Test/Images/Portrait_1.jpg", "Test/OutputImages/Portrait_1.jpg");
        $this->AssertTrue($res);
        $res = $this->UploadBehavior->saveRotatedFile("Test/Images/Portrait_2.jpg", "Test/OutputImages/Portrait_2.jpg");
        $this->AssertTrue($res);
        $res = $this->UploadBehavior->saveRotatedFile("Test/Images/Portrait_3.jpg", "Test/OutputImages/Portrait_3.jpg");
        $this->AssertTrue($res);
        $res = $this->UploadBehavior->saveRotatedFile("Test/Images/Portrait_4.jpg", "Test/OutputImages/Portrait_4.jpg");
        $this->AssertTrue($res);
        $res = $this->UploadBehavior->saveRotatedFile("Test/Images/Portrait_5.jpg", "Test/OutputImages/Portrait_5.jpg");
        $this->AssertTrue($res);
        $res = $this->UploadBehavior->saveRotatedFile("Test/Images/Portrait_6.jpg", "Test/OutputImages/Portrait_6.jpg");
        $this->AssertTrue($res);
        $res = $this->UploadBehavior->saveRotatedFile("Test/Images/Portrait_7.jpg", "Test/OutputImages/Portrait_7.jpg");
        $this->AssertTrue($res);
        $res = $this->UploadBehavior->saveRotatedFile("Test/Images/Portrait_8.jpg", "Test/OutputImages/Portrait_8.jpg");
        $this->AssertTrue($res);
        $res = $this->UploadBehavior->saveRotatedFile("Test/Images/non-exisiting.jpg", "Test/OutputImages/non-exisiting.jpg");
        $this->AssertFalse($res);
    }

    public function test_getDegrees(){
        // EXIF 1 
        $flip = false;
        $res = $this->UploadBehavior->getDegrees("Test/Images/Landscape_1.jpg", $flip);
        $this->AssertEquals($res, 0);
        $this->AssertFalse($flip);
        $res = $this->UploadBehavior->getDegrees("Test/Images/Portrait_1.jpg", $flip);
        $this->AssertEquals($res, 0);
        $this->AssertFalse($flip);

        // EXIF 2 
        $flip = false;
        $res = $this->UploadBehavior->getDegrees("Test/Images/Landscape_2.jpg", $flip);
        $this->AssertEquals($res, 0);
        $this->AssertTrue($flip);
        $res = $this->UploadBehavior->getDegrees("Test/Images/Portrait_2.jpg", $flip);
        $this->AssertEquals($res, 0);
        $this->AssertTrue($flip);

        // EXIF 3 
        $flip = false;
        $res = $this->UploadBehavior->getDegrees("Test/Images/Landscape_3.jpg", $flip);
        $this->AssertEquals($res, 180);
        $this->AssertFalse($flip);
        $res = $this->UploadBehavior->getDegrees("Test/Images/Portrait_3.jpg", $flip);
        $this->AssertEquals($res, 180);
        $this->AssertFalse($flip);

        // EXIF 4 
        $flip = false;
        $res = $this->UploadBehavior->getDegrees("Test/Images/Landscape_4.jpg", $flip);
        $this->AssertEquals($res, 180);
        $this->AssertTrue($flip);
        $res = $this->UploadBehavior->getDegrees("Test/Images/Portrait_4.jpg", $flip);
        $this->AssertEquals($res, 180);
        $this->AssertTrue($flip);

        // EXIF 5 
        $flip = false;
        $res = $this->UploadBehavior->getDegrees("Test/Images/Landscape_5.jpg", $flip);
        $this->AssertEquals($res, 270);
        $this->AssertTrue($flip);
        $res = $this->UploadBehavior->getDegrees("Test/Images/Portrait_5.jpg", $flip);
        $this->AssertEquals($res, 270);
        $this->AssertTrue($flip);

        // EXIF 6 
        $flip = false;
        $res = $this->UploadBehavior->getDegrees("Test/Images/Landscape_6.jpg", $flip);
        $this->AssertEquals($res, 270);
        $this->AssertFalse($flip);
        $res = $this->UploadBehavior->getDegrees("Test/Images/Portrait_6.jpg", $flip);
        $this->AssertEquals($res, 270);
        $this->AssertFalse($flip);

        // EXIF 7 
        $flip = false;
        $res = $this->UploadBehavior->getDegrees("Test/Images/Landscape_7.jpg", $flip);
        $this->AssertEquals($res, 90);
        $this->AssertTrue($flip);
        $res = $this->UploadBehavior->getDegrees("Test/Images/Portrait_7.jpg", $flip);
        $this->AssertEquals($res, 90);
        $this->AssertTrue($flip);

        // EXIF 8 
        $flip = false;
        $res = $this->UploadBehavior->getDegrees("Test/Images/Landscape_8.jpg", $flip);
        $this->AssertEquals($res, 90);
        $this->AssertFalse($flip);
        $res = $this->UploadBehavior->getDegrees("Test/Images/Portrait_8.jpg", $flip);
        $this->AssertEquals($res, 90);
        $this->AssertFalse($flip);
    }
}
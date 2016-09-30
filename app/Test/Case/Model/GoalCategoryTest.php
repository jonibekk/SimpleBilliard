<?php App::uses('GoalousTestCase', 'Test');
App::uses('GoalCategory', 'Model');

/**
 * GoalCategory Test Case
 *
 * @property GoalCategory $GoalCategory
 */
class GoalCategoryTest extends GoalousTestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = array(
        'app.goal_category',
        'app.team',
        'app.goal'
    );

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->GoalCategory = ClassRegistry::init('GoalCategory');
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->GoalCategory);

        parent::tearDown();
    }

    function testGetCategoryListNotEmpty()
    {
        $this->_setDefault();
        $this->GoalCategory->deleteAll(['GoalCategory.team_id' => 1]);
        $this->GoalCategory->save(['team_id' => 1, 'name' => 'test']);
        $actual = $this->GoalCategory->getCategoryList();
        $this->assertCount(1, $actual);
    }

    function testGetCategoryListEmpty()
    {
        $this->_setDefault();
        $this->GoalCategory->deleteAll(['GoalCategory.team_id' => 1]);
        $actual = $this->GoalCategory->getCategoryList();
        $this->assertCount(1, $actual);
    }

    function testGetCategories()
    {
        $this->_setDefault();
        $actual = $this->GoalCategory->getCategories();
        $this->assertCount(2, $actual);
        $this->GoalCategory->save(['team_id' => 1, 'name' => 'test']);
        $actual = $this->GoalCategory->getCategories();
        $this->assertCount(3, $actual);
        $this->GoalCategory->save(['id' => $this->GoalCategory->getLastInsertID(), 'active_flg' => false]);
        $actual = $this->GoalCategory->getCategories();
        $this->assertCount(2, $actual);
    }

    function testSaveDefaultCategory()
    {
        $this->_setDefault();
        $actual = $this->GoalCategory->saveDefaultCategory();
        $this->assertNotEmpty($actual);
    }

    function testSaveGoalCategoriesEmpty()
    {
        $this->_setDefault();
        $actual = $this->GoalCategory->saveGoalCategories([], 1);
        $this->assertFalse($actual);
    }

    function testSaveGoalCategoriesNotEmpty()
    {
        $this->_setDefault();
        $actual = $this->GoalCategory->saveGoalCategories([['name' => 'test'], ['name' => 'test']], 1);
        $this->assertTrue($actual);
    }

    function testSetToInactive()
    {
        $this->_setDefault();
        $actual = $this->GoalCategory->setToInactive(1);
        $this->assertFalse($actual['GoalCategory']['active_flg']);
    }

    function _setDefault()
    {
        $this->GoalCategory->current_team_id = 1;
        $this->GoalCategory->my_uid = 1;
    }
}

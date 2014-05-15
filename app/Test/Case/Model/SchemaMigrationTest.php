<?php
App::uses('SchemaMigration', 'Model');

/**
 * SchemaMigration Test Case

 */
class SchemaMigrationTest extends CakeTestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = array(
        'app.schema_migration'
    );

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->SchemaMigration = ClassRegistry::init('SchemaMigration');
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->SchemaMigration);

        parent::tearDown();
    }

}

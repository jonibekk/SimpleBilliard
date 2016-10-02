<?php App::uses('GoalousTestCase', 'Test');
App::uses('EvaluateScore', 'Model');

/**
 * EvaluateScore Test Case
 *
 * @property EvaluateScore $EvaluateScore
 */
class EvaluateScoreTest extends GoalousTestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = array(
        'app.team',
        'app.evaluate_score'
    );

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->EvaluateScore = ClassRegistry::init('EvaluateScore');
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->EvaluateScore);

        parent::tearDown();
    }

    function testGetScoreList()
    {
        $teamId = 1;
        $this->EvaluateScore->deleteAll(['team_id' => $teamId]);
        $data = [
            [
                'team_id'     => 1,
                'name'        => 'A++',
                'description' => 'aiueo',
                'index_num'   => 0
            ],
            [
                'team_id'     => 1,
                'name'        => 'A+',
                'description' => 'kakikukeko',
                'index_num'   => 1
            ],
            [
                'team_id'     => 2,
                'name'        => 'A++',
                'description' => 'sasisuseso',
                'index_num'   => 1
            ],
            [
                'team_id'     => 1,
                'name'        => 'A',
                'description' => 'tatituteto',
                'index_num'   => 2
            ],
            [
                'team_id'     => 1,
                'name'        => 'B',
                'description' => 'naninuneno',
                'index_num'   => 3
            ],
            [
                'team_id'     => 2,
                'name'        => 'A+',
                'description' => 'hahihuheho',
                'index_num'   => 2
            ]
        ];
        $this->EvaluateScore->saveAll($data);
        // plus 1 because add null index in getScoreList()
        $expected = count(Hash::extract($data, "{n}[team_id={$teamId}]")) + 1;

        $res = $this->EvaluateScore->getScoreList(1);
        $this->assertEquals(count($res), $expected);
    }

    function testGetScore()
    {
        $actual = $this->EvaluateScore->getScore(1);
        $this->assertNotEmpty($actual['EvaluateScore']);

        $this->EvaluateScore->updateAll(['active_flg' => false], ['EvaluateScore.team_id' => 1]);
        $actual = $this->EvaluateScore->getScore(1);
        $this->assertEmpty($actual['EvaluateScore']);
    }

    function testSaveScores()
    {
        $actual = $this->EvaluateScore->saveScores([], 1);
        $this->assertFalse($actual);

        $actual = $this->EvaluateScore->saveScores([['name' => 'test'], ['name' => 'test']], 1);
        $this->assertTrue($actual);
    }

    function testSetToInactive()
    {
        $this->EvaluateScore->save(['name' => 'test', 'team_id' => 1]);
        $actual = $this->EvaluateScore->setToInactive($this->EvaluateScore->getLastInsertID());
        $this->assertFalse($actual['EvaluateScore']['active_flg']);
    }

}

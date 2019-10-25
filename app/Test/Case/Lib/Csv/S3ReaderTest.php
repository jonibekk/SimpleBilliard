<?php

App::uses('GoalousTestCase', 'Test');
App::import('Lib/Csv', 'S3Reader');

/**
 * Class S3ReaderTest
 */
class S3ReaderTest extends GoalousTestCase
{
    /**
     * @return array
     */
    public function dataProviderNormalGetRecords(): array
    {
        return [
            'normal: have not header' => [
                'header' => [],
                'records' => [
                    ['hoge@isao.co.jp', 'Hoge', 'Yamada', 'on', 'jpn'],
                    ['fuga@isao.co.jp', 'Fuga', 'John', 'off', 'eng'],
                    ['piyo@isao.co.jp', 'Piyo', 'Cheng', 'off', 'chn'],
                ],
                'expected' => [
                    ['hoge@isao.co.jp', 'Hoge', 'Yamada', 'on', 'jpn'],
                    ['fuga@isao.co.jp', 'Fuga', 'John', 'off', 'eng'],
                    ['piyo@isao.co.jp', 'Piyo', 'Cheng', 'off', 'chn'],
                ],
            ],
            'normal: have header' => [
                'header' => ['h1', 'h2', 'h3', 'h4', 'h5'],
                'records' => [
                    ['hoge@isao.co.jp', 'Hoge', 'Yamada', 'on', 'jpn'],
                    ['fuga@isao.co.jp', 'Fuga', 'John', 'off', 'eng'],
                    ['piyo@isao.co.jp', 'Piyo', 'Cheng', 'on', 'chn'],
                ],
                'expected' => [
                    ['h1' => 'hoge@isao.co.jp', 'h2' => 'Hoge', 'h3' => 'Yamada', 'h4' => 'on', 'h5' => 'jpn'],
                    ['h1' => 'fuga@isao.co.jp', 'h2' => 'Fuga', 'h3' => 'John', 'h4' => 'off', 'h5' => 'eng'],
                    ['h1' => 'piyo@isao.co.jp', 'h2' => 'Piyo', 'h3' => 'Cheng', 'h4' => 'on', 'h5' => 'chn'],
                ],
            ],
            'normal: have not header and ignore empty lines' => [
                'header' => [],
                'records' => [
                    [null],
                    ['hoge@isao.co.jp', 'Hoge', 'Yamada', 'on', 'jpn'],
                    [null],
                    ['fuga@isao.co.jp', 'Fuga', 'John', 'off', 'eng'],
                    [null],
                    ['piyo@isao.co.jp', 'Piyo', 'Cheng', 'off', 'chn'],
                    [null],
                ],
                'expected' => [
                    [],
                    ['hoge@isao.co.jp', 'Hoge', 'Yamada', 'on', 'jpn'],
                    [],
                    ['fuga@isao.co.jp', 'Fuga', 'John', 'off', 'eng'],
                    [],
                    ['piyo@isao.co.jp', 'Piyo', 'Cheng', 'off', 'chn'],
                    [],
                ],
            ],
            'normal: have header and ignore empty lines' => [
                'header' => ['h1', 'h2', 'h3', 'h4', 'h5'],
                'records' => [
                    [null],
                    ['hoge@isao.co.jp', 'hoge', 'yamada', 'on', 'jpn'],
                    [null],
                    ['fuga@isao.co.jp', 'fuga', 'john', 'off', 'eng'],
                    [null],
                    ['piyo@isao.co.jp', 'piyo', 'cheng', 'on', 'chn'],
                    [null],
                ],
                'expected' => [
                    [],
                    ['h1' => 'hoge@isao.co.jp', 'h2' => 'hoge', 'h3' => 'yamada', 'h4' => 'on', 'h5' => 'jpn'],
                    [],
                    ['h1' => 'fuga@isao.co.jp', 'h2' => 'fuga', 'h3' => 'john', 'h4' => 'off', 'h5' => 'eng'],
                    [],
                    ['h1' => 'piyo@isao.co.jp', 'h2' => 'piyo', 'h3' => 'cheng', 'h4' => 'on', 'h5' => 'chn'],
                    [],
                ],
            ],
        ];
    }

    /**
     * @group getRecords
     * @param array $header
     * @param array $records
     * @param array $expected
     * @dataProvider dataProviderNormalGetRecords
     */
    public function testGetRecordsNormal(array $header, array $records, array $expected)
    {
        $readerMock = $this->getS3ReaderMock($header, $records);
        $actual = $readerMock->getRecords();
        $this->assertEquals($expected, $actual);
    }

    /**
     * @return array
     */
    public function dataProviderAbnormalGetRecords(): array
    {
        return [
            'abnormal: Many header columns' => [
                'header' => ['h1', 'h2', 'h3', 'h4', 'h5'],
                'records' => [
                    ['hoge@isao.co.jp', 'Hoge', 'Yamada', 'on', 'jpn'],
                    ['fuga@isao.co.jp', 'Fuga', 'John', 'off', 'eng', 'over'],
                    ['piyo@isao.co.jp', 'Piyo', 'Cheng', 'on', 'chn'],
                ]
            ],
            'abnormal: Many record columns' => [
                'header' => ['h1', 'h2', 'h3', 'h4', 'h5', 'over'],
                'records' => [
                    ['hoge@isao.co.jp', 'Hoge', 'Yamada', 'on', 'jpn'],
                    ['fuga@isao.co.jp', 'Fuga', 'John', 'off', 'eng'],
                    ['piyo@isao.co.jp', 'Piyo', 'Cheng', 'on', 'chn'],
                ]
            ],
        ];
    }

    /**
     * @group getRecords
     * @param array $header
     * @param array $records
     * @dataProvider dataProviderAbnormalGetRecords
     * @expectedException \RuntimeException
     */
    public function testGetRecordsAbnormal(array $header, array $records)
    {
        $readerMock = $this->getS3ReaderMock($header, $records);
        $readerMock->getRecords();
    }

    /**
     * @param array $header
     * @param array $records
     * @return \Mockery\Mock
     */
    private function getS3ReaderMock(array $header, array $records)
    {
        $readerMock = \Mockery::mock(S3Reader::class)->shouldAllowMockingProtectedMethods()->makePartial();
        $readerMock->shouldReceive('setHeader')->passthru();
        $readerMock->shouldReceive('getRecords')->passthru();
        $readerMock->shouldReceive('getOriginRecords')->andReturn($records);

        $readerMock->setHeader($header);
        return $readerMock;
    }

    public function tearDown()
    {
        parent::tearDown();
        Mockery::close();
    }
}

<?php App::uses('CakeTestFixtureEx', 'Test/Fixture');

/**
 * SearchPostFileFixture
 */
class SearchPostFileFixture extends CakeTestFixtureEx
{

    /**
     * Fields
     *
     * @var array
     */
    public $fields = array(
        'id' => array(
            'type'     => 'biginteger',
            'null'     => false,
            'default'  => null,
            'unsigned' => true,
            'key'      => 'primary',
            'comment'  => 'SearchPostFileID'
        ),
        'team_id' => array(
            'type'     => 'biginteger',
            'null'     => false,
            'default'  => null,
            'unsigned' => true,
            'comment'  => 'TeamID'
        ),
        'user_id' => array(
            'type'     => 'biginteger',
            'null'     => true,
            'default'  => null,
            'unsigned' => true,
            'comment'  => 'XXXXXXXXXX'
        ),
        'circle_id' => array(
            'type'     => 'biginteger',
            'null'     => true,
            'default'  => null,
            'unsigned' => true,
            'comment'  => 'CircleID'
        ),
        'post_id' => array(
            'type'     => 'biginteger',
            'null'     => true,
            'default'  => null,
            'unsigned' => true,
            'comment'  => 'PostID'
        ),
        'comment_id' => array(
            'type'     => 'biginteger',
            'null'     => true,
            'default'  => null,
            'unsigned' => true,
            'comment'  => 'CommentID'
        ),
        'attached_file_id' => array(
            'type'     => 'biginteger',
            'null'     => true,
            'default'  => null,
            'unsigned' => true,
            'comment'  => 'AttachedFileID'
        ),
        'video_stream_id' => array(
            'type'     => 'biginteger',
            'null'     => true,
            'default'  => null,
            'unsigned' => true,
            'comment'  => 'VideoSteamID'
        ),
        'created'                      => array(
            'type'     => 'integer',
            'null'     => true,
            'default'  => null,
            'unsigned' => true,
            'comment'  => 'チームを追加した日付時刻'
        ),
        'modified'                     => array(
            'type'     => 'integer',
            'null'     => true,
            'default'  => null,
            'unsigned' => true,
            'comment'  => 'チームを更新した日付時刻'
        )
    );

    /**
     * Records
     *
     * @var array
     */
    public $records = array();
}

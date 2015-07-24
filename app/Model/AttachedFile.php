<?php
App::uses('AppModel', 'Model');

/**
 * AttachedFile Model
 *
 * @property User        $User
 * @property Team        $Team
 * @property CommentFile $CommentFile
 * @property PostFile    $PostFile
 */
class AttachedFile extends AppModel
{
    /**
     * file type
     */
    const TYPE_FILE_IMG = 0;
    const TYPE_FILE_VIDEO = 1;
    const TYPE_FILE_DOC = 2;

    static public $TYPE_FILE = [
        self::TYPE_FILE_IMG   => null,
        self::TYPE_FILE_VIDEO => null,
        self::TYPE_FILE_DOC   => null,
    ];

    function _setFileTypeName()
    {
        self::$TYPE_FILE[self::TYPE_FILE_IMG] = __d('gl', "画像");
        self::$TYPE_FILE[self::TYPE_FILE_VIDEO] = __d('gl', "動画");
        self::$TYPE_FILE[self::TYPE_FILE_DOC] = __d('gl', "ドキュメント");
    }

    function __construct($id = false, $table = null, $ds = null)
    {
        parent::__construct($id, $table, $ds);

        $this->_setFileTypeName();
    }

    /**
     * model type
     */
    const TYPE_MODEL_POST = 0;
    const TYPE_MODEL_COMMENT = 1;
    static public $TYPE_MODEL = [
        self::TYPE_MODEL_POST    => [
            'intermediateModel' => 'PostFile',
            'foreign_key'       => 'PostFile',
        ],
        self::TYPE_MODEL_COMMENT => [
            'intermediateModel' => 'CommentFile',
        ],
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public $validate = [
        'file_type'  => [
            'numeric' => [
                'rule' => ['numeric'],
            ],
        ],
        'file_size'  => [
            'numeric' => [
                'rule' => ['numeric'],
            ],
        ],
        'model_type' => [
            'numeric' => [
                'rule' => ['numeric'],
            ],
        ],
        'del_flg'    => [
            'boolean' => [
                'rule' => ['boolean'],
            ],
        ],
    ];

    /**
     * belongsTo associations
     *
     * @var array
     */
    public $belongsTo = [
        'User',
        'Team',
    ];

    /**
     * hasMany associations
     *
     * @var array
     */
    public $hasMany = [
        'CommentFile',
        'PostFile',
    ];

    /**
     * 共通ファイル保存処理
     * $model_type should be in self::$TYPE_FILE
     *
     * @param       $primary_id
     * @param       $model_type
     * @param array $file_hashes
     *
     * @return bool
     */
    public function saveRelatedFiles($primary_id, $model_type, $file_hashes = [])
    {
        if (true) {
            return true;
        }
        return false;

    }

    /**
     * 共通ファイル削除処理
     * $model_type should be in self::$TYPE_FILE
     *
     * @param       $primary_id
     * @param       $model_type
     *
     * @return bool
     */
    public function deleteRelatedFiles($primary_id, $model_type)
    {
        if (true) {
            return true;
        }
        return false;

    }

    /**
     * ファイルの仮アップロード
     *
     * @param $postData
     *
     * @return false|string
     */
    public function preUploadFile($postData)
    {
        if (true) {
            return $hashed_key = 'r3j21iop3ijodsa';
        }
        return false;
    }

    /**
     * ファイルアップロードのキャンセル
     *
     * @param $hashed_key
     *
     * @return false|string
     */
    public function cancelUploadFile($hashed_key)
    {
        if (true) {
            return $hashed_key = 'r3j21iop3ijodsa';
        }
        return false;
    }
}

<?php
App::uses('AppModel', 'Model');
use Aws\Common\Aws;
use Aws\S3;
use Aws\S3\S3Client;
use Aws\Common\Enum\Region;
use Guzzle\Http\EntityBody;
use Aws\S3\Enum\CannedAcl;
use Aws\S3\Exception\S3Exception;

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
        self::TYPE_FILE_IMG   => ['name' => null, 'type' => 'image'],
        self::TYPE_FILE_VIDEO => ['name' => null, 'type' => 'video'],
        self::TYPE_FILE_DOC   => ['name' => null, 'type' => 'etc'],
    ];

    function _setFileTypeName()
    {
        self::$TYPE_FILE[self::TYPE_FILE_IMG]['name'] = __d('gl', "画像");
        self::$TYPE_FILE[self::TYPE_FILE_VIDEO]['name'] = __d('gl', "動画");
        self::$TYPE_FILE[self::TYPE_FILE_DOC]['name'] = __d('gl', "ドキュメント");
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
            'foreign_key'       => 'post_id',
        ],
        self::TYPE_MODEL_COMMENT => [
            'intermediateModel' => 'CommentFile',
            'foreign_key'       => 'post_id',
        ],
    ];
    /**
     * aws s3用オブジェクト
     *
     * @var  S3Client $s3
     */
    private $s3;

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
     * ファイルの仮アップロード
     *
     * @param array $postData
     *
     * @return false|string
     */
    public function preUploadFile($postData)
    {
        if (!$file_info = viaIsSet($postData['file'])) {
            return false;
        }
        /** @var GlRedis $Redis */
        $Redis = ClassRegistry::init('GlRedis');
        $hashed_key = $Redis->savePreUploadFile($file_info, $this->current_team_id, $this->my_uid);
        return $hashed_key;
    }

    /**
     * ファイルアップロードのキャンセル
     *
     * @param string $hashed_key
     *
     * @return bool
     */
    public function cancelUploadFile($hashed_key)
    {
        if (is_null($hashed_key)) {
            return false;
        }
        /** @var GlRedis $Redis */
        $Redis = ClassRegistry::init('GlRedis');
        $res = $Redis->delPreUploadedFile($this->current_team_id, $this->my_uid, $hashed_key);
        return (bool)$res;
    }

    /**
     * 利用不可能なモデルタイプを指定されているか？
     * $model_type should be in self::$TYPE_FILE
     *
     * @param $model_type
     *
     * @return bool
     */
    function isUnavailableModelType($model_type)
    {
        return !array_key_exists($model_type, self::$TYPE_MODEL);
    }

    /**
     * 共通のファイル保存処理
     * $model_type should be in self::$TYPE_FILE
     *
     * @param integer $foreign_key_id
     * @param integer $model_type
     * @param array   $file_hashes
     *
     * @return bool
     */
    public function saveRelatedFiles($foreign_key_id, $model_type, $file_hashes = [])
    {
        if ($this->isUnavailableModelType($model_type) || empty($file_hashes)) {
            return false;
        }
        /** @var GlRedis $Redis */
        $Redis = ClassRegistry::init('GlRedis');
        $attached_file_common_data = [
            'user_id'    => $this->my_uid,
            'team_id'    => $this->current_team_id,
            'model_type' => $model_type,
        ];

        //保存データ生成
        $file_datas = [];
        foreach ($file_hashes as $hash) {
            $file = $Redis->getPreUploadedFile($this->current_team_id, $this->my_uid, $hash);
            $file_data = $attached_file_common_data;
            $file_data['file_name'] = $file['info']['name'];
            $file_data['file_size'] = $file['info']['size'];
            $file_data['file_ext'] = substr($file['info']['name'], strrpos($file['info']['name'], '.') + 1);
            $file_data['file_type'] = $this->getFileType($file['info']['type']);
            $file_datas[] = $file_data;
        }
        $model = self::$TYPE_MODEL[$model_type];
        foreach ($file_datas as $file_data) {
            $save_data = [
                $model['intermediateModel'] => [
                    [
                        $model['foreign_key'] => $foreign_key_id,
                        'user_id'             => $this->my_uid,
                        'team_id'             => $this->current_team_id,
                    ],
                ],
                'AttachedFile'              => $file_data
            ];
            if (!$this->saveAll($save_data)) {
                return false;
            }
        }
        return true;
    }

    /**
     * $type: (e.g.) 'image/jpeg'
     *
     * @param $type
     *
     * @return int|string
     */
    function getFileType($type)
    {
        $arr = explode('/', $type);
        $arr[0];

        foreach (self::$TYPE_FILE as $file_type => $v) {
            if ($arr[0] == $v['type']) {
                return $file_type;
            }
        }

        return self::TYPE_FILE_DOC;
    }

    /**
     * 共通のファイル削除処理(全ての紐付いた画像)
     * $model_type should be in self::$TYPE_FILE
     *
     * @param  integer $foreign_key_id
     * @param  integer $model_type
     *
     * @return bool
     */
    public function deleteAllRelatedFiles($foreign_key_id, $model_type)
    {
        if ($this->isUnavailableModelType($model_type)) {
            return false;
        }
        return true;
    }

    /**
     * 共通のファイル単体の削除処理
     * $model_type should be in self::$TYPE_FILE
     *
     * @param integer $attached_file_id
     * @param integer $model_type
     *
     * @return bool
     */
    public function deleteRelatedFile($attached_file_id, $model_type)
    {
        if ($this->isUnavailableModelType($model_type)) {
            return false;
        }
        return true;
    }

    function _setupS3()
    {
        if ($this->s3) {
            return;
        }
        // S3を操作するためのオブジェクトを生成（リージョンは東京）
        $this->s3 = Aws::factory(
            array(
                'key'    => AWS_ACCESS_KEY,
                'secret' => AWS_SECRET_KEY,
                'region' => Region::AP_NORTHEAST_1
            ))
                       ->get('s3');
    }
    function s3Delete($from_path)
    {
        //公開環境じゃない場合は処理しない
        if (!PUBLIC_ENV) {
            return false;
        }
        $img_path_exp = explode(S3_TRIM_PATH, $from_path);
        $to_path = $img_path_exp[1];
        try {
            $response = $this->s3->deleteObject(['Bucket' => S3_ASSETS_BUCKET, 'Key' => $to_path]);
            return $response;
        } catch (S3Exception $e) {
        }
        return null;
    }

    function s3Upload($from_path, $type)
    {
        //公開環境じゃない場合は処理しない
        if (!PUBLIC_ENV) {
            return false;
        }

        $path_exp = explode(S3_TRIM_PATH, $from_path);
        $to_path = $path_exp[1];

        try {
            $response = $this->s3
                ->putObject(
                    array(
                        'Bucket'               => S3_ASSETS_BUCKET,
                        'Key'                  => $to_path,
                        'Body'                 => EntityBody::factory(fopen($from_path, 'r')),
                        'ContentType'          => $type,
                        'StorageClass'         => 'STANDARD',
                        'ServerSideEncryption' => 'AES256',
                        'ACL'                  => CannedAcl::AUTHENTICATED_READ
                    ));
            return $response;

        } catch (S3Exception $e) {
        }
        return null;
    }

}

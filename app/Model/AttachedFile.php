<?php
App::uses('AppModel', 'Model');

/**
 * AttachedFile Model
 *
 * @property User             $User
 * @property Team             $Team
 * @property CommentFile      $CommentFile
 * @property PostFile         $PostFile
 * @property ActionResultFile $ActionResultFile
 */
class AttachedFile extends AppModel
{
    /**
     * file type
     */
    const TYPE_FILE_IMG = 0;
    const TYPE_FILE_VIDEO = 1;
    const TYPE_FILE_DOC = 2;

    /**
     * フロントエンド含めすべての添付ファイル(画像含む)のサイズ上限チェックにこのルールを適用する
     */
    const ATTACHABLE_MAX_FILE_SIZE_MB = 25;
    /**
     * アップロード可能な画像ファイルの画素数
     */
    const ATTACHABLE_MAX_PIXEL = 25000000;//2500万画素

    static public $TYPE_FILE = [
        self::TYPE_FILE_IMG   => ['name' => null, 'type' => 'image'],
        self::TYPE_FILE_VIDEO => ['name' => null, 'type' => 'video'],
        self::TYPE_FILE_DOC   => ['name' => null, 'type' => 'etc'],
    ];

    function _setFileTypeName()
    {
        self::$TYPE_FILE[self::TYPE_FILE_IMG]['name'] = __("Images");
        self::$TYPE_FILE[self::TYPE_FILE_VIDEO]['name'] = __("Movies");
        self::$TYPE_FILE[self::TYPE_FILE_DOC]['name'] = __("Document");
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
    const TYPE_MODEL_ACTION_RESULT = 2;
    static public $TYPE_MODEL = [
        self::TYPE_MODEL_POST          => [
            'intermediateModel' => 'PostFile',
            'foreign_key'       => 'post_id',
        ],
        self::TYPE_MODEL_COMMENT       => [
            'intermediateModel' => 'CommentFile',
            'foreign_key'       => 'comment_id',
        ],
        self::TYPE_MODEL_ACTION_RESULT => [
            'intermediateModel' => 'ActionResultFile',
            'foreign_key'       => 'action_result_id',
        ],
    ];
    /**
     * @var array $saved_datas
     */
    private $saved_datas;

    /**
     * Validation rules
     *
     * @var array
     */
    public $validate = [
        'attached'   => [
            'image_max_size' => ['rule' => ['attachmentMaxSize', self::ATTACHABLE_MAX_FILE_SIZE_MB * 1024 * 1024],],
            // convert to byte
        ],
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
    public $actsAs = [
        'Upload' => [
            'attached' => [
                'styles'                 => [
                    'x_small' => '128l',
                    'small'   => '648W',
                    'large'   => '2048W',
                ],
                'path'                   => ":webroot/upload/:model/:id/:hash_:style.:extension",
                'quality'                => 60,
                'default_url'            => 'no-image.jpg',
                'addFieldNameOnFileName' => false,
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
        'CommentFile'      => [
            'dependent' => true,
        ],
        'PostFile'         => [
            'dependent' => true,
        ],
        'ActionResultFile' => [
            'dependent' => true,
        ],
    ];

    public function getFileTypeOptions()
    {
        $res = [null => __("All")];
        foreach (self::$TYPE_FILE as $v) {
            $res[$v['type']] = $v['name'];
        }
        return $res;
    }

    public function getFileTypeId($file_type)
    {
        $res = null;
        foreach (self::$TYPE_FILE as $k => $v) {
            if ($file_type === $v['type']) {
                return $k;
            }
        }
        return $res;
    }

    /**
     * ファイルの仮アップロード
     *
     * @param array $postData
     *
     * @return string
     * @throws Exception エラーメッセージ
     */
    public function preUploadFile(array $postData): string
    {
        if (!$file_info = Hash::get($postData, 'file')) {
            $this->log(sprintf("[%s] file not exists.", __METHOD__));
            $this->log(sprintf("PostData: %s", var_export($postData, true)));
            $this->log(Debugger::trace());
            throw new Exception(__('Failed to upload.'));
        }
        //ファイル上限チェック
        if ($file_info['size'] > self::ATTACHABLE_MAX_FILE_SIZE_MB * 1024 * 1024) {
            throw new Exception(__("%sMB is the limit.", self::ATTACHABLE_MAX_FILE_SIZE_MB));
        }
        //ファイルの画素数チェック
        if (strpos($file_info['type'], 'image') !== false) {
            list($imgWidth, $imgHeight) = getimagesize($file_info['tmp_name']);
            if ($imgWidth * $imgHeight > self::ATTACHABLE_MAX_PIXEL) {
                throw new Exception(__("%s pixel is the limit.", self::ATTACHABLE_MAX_PIXEL));
            }
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
        foreach ($file_hashes as $i => $hash) {
            $file = $Redis->getPreUploadedFile($this->current_team_id, $this->my_uid, $hash);
            file_put_contents($file['info']['tmp_name'], $file['content']);
            $file_data = $attached_file_common_data;

            $file_data['attached'] = $file['info'];
            $file_data['file_size'] = $file['info']['size'];
            $file_data['file_ext'] = substr($file['info']['name'], strrpos($file['info']['name'], '.') + 1);
            $file_data['file_type'] = $this->getFileType($file['info']['type']);
            //アクションの場合は１枚目のファイルをファイル一覧に表示しない及び削除不可能にする
            if ($i === 0 && $model_type == self::TYPE_MODEL_ACTION_RESULT) {
                $file_data['display_file_list_flg'] = false;
                $file_data['removable_flg'] = false;
            }
            $file_datas[] = $file_data;
        }
        $model = self::$TYPE_MODEL[$model_type];
        foreach ($file_datas as $index => $file_data) {
            $save_data = [
                $model['intermediateModel'] => [
                    [
                        $model['foreign_key'] => $foreign_key_id,
                        'user_id'             => $this->my_uid,
                        'team_id'             => $this->current_team_id,
                        'index_num'           => $index,
                    ],
                ],
                'AttachedFile'              => $file_data
            ];
            if (!$res = $this->saveAll($save_data)) {
                return false;
            }
        }
        return true;
    }

    /**
     * 共通のファイル更新処理
     * $model_type should be in self::$TYPE_FILE
     * $update_files: 一時ファイルハッシュ、もしくはModelのidがindex順にわたってくる
     *
     * @param integer $foreign_key_id
     * @param integer $model_type
     * @param array   $update_files
     * @param array   $delete_files
     *
     * @return bool
     */
    public function updateRelatedFiles($foreign_key_id, $model_type, $update_files = [], $delete_files = [])
    {
        if ($this->isUnavailableModelType($model_type)) {
            return false;
        }
        //ファイル削除処理
        foreach ($delete_files as $id) {
            $this->delete($id);
        }

        /** @var GlRedis $Redis */
        $Redis = ClassRegistry::init('GlRedis');
        $attached_file_common_data = [
            'user_id'    => $this->my_uid,
            'team_id'    => $this->current_team_id,
            'model_type' => $model_type,
        ];

        $model = self::$TYPE_MODEL[$model_type];
        //保存データ生成
        foreach ($update_files as $i => $id_or_hash) {
            //既存のIDの場合はindexの更新のみ
            if (is_numeric($id_or_hash)) {
                //アクションのメイン画像の場合は更新しない
                if ($i === 0 && $model_type == self::TYPE_MODEL_ACTION_RESULT) {
                    continue;
                }
                $this->{$model['intermediateModel']}->updateAll(['index_num' => $i],
                    [$model['intermediateModel'] . ".attached_file_id" => $id_or_hash]);
                continue;
            }

            //$id_or_hashがstringつまり、新規のファイルの場合は登録処理を行う
            $file = $Redis->getPreUploadedFile($this->current_team_id, $this->my_uid, $id_or_hash);
            file_put_contents($file['info']['tmp_name'], $file['content']);
            $file_data = $attached_file_common_data;

            $file_data['attached'] = $file['info'];
            $file_data['file_size'] = $file['info']['size'];
            $file_data['file_ext'] = substr($file['info']['name'], strrpos($file['info']['name'], '.') + 1);
            $file_data['file_type'] = $this->getFileType($file['info']['type']);
            //アクションの場合は１枚目のファイルをファイル一覧に表示しない及び削除不可能にする
            if ($i === 0 && $model_type == self::TYPE_MODEL_ACTION_RESULT) {
                $file_data['display_file_list_flg'] = false;
                $file_data['removable_flg'] = false;
            }
            $save_data = [
                $model['intermediateModel'] => [
                    [
                        $model['foreign_key'] => $foreign_key_id,
                        'user_id'             => $this->my_uid,
                        'team_id'             => $this->current_team_id,
                        'index_num'           => $i,
                    ],
                ],
                'AttachedFile'              => $file_data
            ];
            if (!$res = $this->saveAll($save_data)) {
                return false;
            }

        }
        return true;
    }

    function afterSave($created, $options = array())
    {
        if ($created) {
            $this->saved_datas[] = $this->data[$this->name];
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
        $model = self::$TYPE_MODEL[$model_type];
        $related_files = $this->{$model['intermediateModel']}->find('all', [
            'conditions' => [$model['foreign_key'] => $foreign_key_id],
            'fields'     => ['id', 'attached_file_id']
        ]);
        //deleteAllだとsoftDeleteされない為、foreach
        foreach ($related_files as $related_file) {
            $this->{$model['intermediateModel']}->delete($related_file[$model['intermediateModel']]['id']);
            $this->delete($related_file[$model['intermediateModel']]['attached_file_id']);
        }
        return true;
    }

    /**
     * @param      $foreign_key_id
     * @param      $model_type
     * @param null $file_type
     *
     * @return array|bool|int|null
     */
    public function getCountOfAttachedFiles($foreign_key_id, $model_type, $file_type = null)
    {
        if ($this->isUnavailableModelType($model_type)) {
            return false;
        }
        $model = self::$TYPE_MODEL[$model_type];
        $related_files = $this->{$model['intermediateModel']}->find('list', [
            'conditions' => [$model['foreign_key'] => $foreign_key_id],
            'fields'     => ['attached_file_id', 'attached_file_id']
        ]);
        if ($file_type === null) {
            return count($related_files);
        }

        $options = [
            'conditions' => [
                'id'        => $related_files,
                'file_type' => $file_type,
            ]
        ];
        $count_attached_files = $this->find('count', $options);
        return $count_attached_files;
    }

    /**
     * ファイルが閲覧/ダウンロード可能か確認
     *
     * @param int $file_id 添付ファイルのID
     *
     * @return bool
     */
    public function isReadable($file_id)
    {
        $file = $this->findById($file_id);
        if (!$file) {
            return false;
        }

        // アクションに添付されたファイルは誰でも閲覧可能
        if ($file['AttachedFile']['model_type'] == self::TYPE_MODEL_ACTION_RESULT) {
            return true;
        }

        // 投稿とコメントへの添付ファイルの場合、その投稿自体が閲覧可能かを確認する。
        // アクションへのコメントの場合は、だれでも閲覧可能にする
        $post_id = "";

        // 投稿への添付ファイル
        // 関連テーブルから post_id 取得
        if ($file['AttachedFile']['model_type'] == self::TYPE_MODEL_POST) {
            $modelName = self::$TYPE_MODEL[self::TYPE_MODEL_POST]['intermediateModel'];
            $row = $this->{$modelName}->find('first', [
                'conditions' => [
                    'attached_file_id' => $file['AttachedFile']['id'],
                ],
            ]);
            $post_id = $row[$modelName]['post_id'];
        }
        // コメントへの添付ファイル
        // 関連テーブルから post_id 取得
        elseif ($file['AttachedFile']['model_type'] == self::TYPE_MODEL_COMMENT) {
            $modelName = self::$TYPE_MODEL[self::TYPE_MODEL_COMMENT]['intermediateModel'];
            $row = $this->{$modelName}->find('first', [
                'conditions' => [
                    'attached_file_id' => $file['AttachedFile']['id'],
                ],
                'contain'    => [
                    'Comment' => ['Post']
                ]
            ]);

            // アクションへのコメントの場合は誰でも閲覧可能
            if ($row['Comment']['Post']['type'] == Post::TYPE_ACTION) {
                return true;
            }

            $post_id = $row['Comment']['post_id'];
        }

        // 以下の場合は閲覧可能
        if (
            // 自分の投稿の場合
            $this->PostFile->Post->isMyPost($post_id) ||
            // 公開サークルに共有されている場合
            $this->PostFile->Post->PostShareCircle->isShareWithPublicCircle($post_id) ||
            // 自分が個人として共有されている場合
            $this->PostFile->Post->PostShareUser->isShareWithMe($post_id) ||
            // 自分の所属しているサークルに共有されている場合
            $this->PostFile->Post->PostShareCircle->isMyCirclePost($post_id)
        ) {
            return true;
        }

        return false;
    }

    /**
     * ファイルのURLを返す
     *
     * @param $file_id
     *
     * @return bool|null|string
     */
    public function getFileUrl($file_id)
    {
        $file = $this->findById($file_id);
        if (!$file) {
            return false;
        }

        App::uses('UploadHelper', 'View/Helper');
        $upload = new UploadHelper(new View());
        return $upload->attachedFileUrl($file, 'download');
    }
}

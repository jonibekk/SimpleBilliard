<?php
App::uses('AppModel', 'Model');
App::import('Model/Entity', 'AttachedFileEntity');
App::import('Service', 'PostResourceService');
App::uses('PostResource', 'Model');
App::import('Service', 'PostFileService');

use Goalous\Enum as Enum;
use Goalous\Enum\DataType\DataType as DataType;

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
     *
     * @deprecated use Goalous\Enum\Model\AttachedFile\AttachedFileType
     */
    const TYPE_FILE_IMG = 0;
    /**
     * @deprecated use Goalous\Enum\Model\AttachedFile\AttachedFileType
     */
    const TYPE_FILE_VIDEO = 1;
    /**
     * @deprecated use Goalous\Enum\Model\AttachedFile\AttachedFileType
     */
    const TYPE_FILE_DOC = 2;

    /**
     * フロントエンド含めすべての添付ファイル(画像含む)のサイズ上限チェックにこのルールを適用する
     */
    const ATTACHABLE_MAX_FILE_SIZE_MB = 100;
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
     * file type
     *
     * @deprecated use Goalous\Enum\Model\AttachedFile\AttachedModelType
     */
    const TYPE_MODEL_POST = 0;
    /**
     * @deprecated use Goalous\Enum\Model\AttachedFile\AttachedModelType
     */
    const TYPE_MODEL_COMMENT = 1;
    /**
     * @deprecated use Goalous\Enum\Model\AttachedFile\AttachedModelType
     */
    const TYPE_MODEL_ACTION_RESULT = 2;
    /**
     * @deprecated use Goalous\Enum\Model\AttachedFile\AttachedModelType
     */
    const TYPE_MODEL_MESSAGE = 3;

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
        self::TYPE_MODEL_MESSAGE       => [
            'intermediateModel' => 'MessageFile',
            'foreign_key'       => 'message_id',
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
            // convert to byte
            'canProcessImage' => ['rule' => 'canProcessImage',],
            'image_max_size'  => ['rule' => ['attachmentMaxSize', self::ATTACHABLE_MAX_FILE_SIZE_MB * 1024 * 1024],],
            'image_type'      => ['rule' => ['attachmentImageType',],],
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
        'MessageFile'      => [
            'dependent' => true,
        ],
    ];

    public $modelConversionTable = [
        'user_id'          => DataType::INT,
        'team_id'          => DataType::INT,
        'file_type'        => DataType::INT,
        'file_size'        => DataType::INT,
        'model_type'       => DataType::INT,
        'display_list_flg' => DataType::BOOL,
        'removable_flg'    => DataType::BOOL
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
     * @param boolean $hasVideoStream Set true if having video stream resource
     *
     * @return bool
     */
    public function saveRelatedFiles($foreign_key_id, $model_type, $file_hashes = [], $hasVideoStream = false)
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
            // for error log in https://goalous.slack.com/archives/C0LV38PC6/p1497691605323876
            // TODO: I dont't know the cause of above error. So, logging it.
            if (empty($file)) {
                $this->log(sprintf("failed to restore file! hash: %s, teamId: %s, loggedIn user: %s, foreign_key_id: %s, model_type: %s",
                    $hash, $this->current_team_id, $this->my_uid, $foreign_key_id, $model_type));
                $this->log(Debugger::trace());
            }

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
                        'index_num'           => $index + ($hasVideoStream ? 1 : 0),
                    ],
                ],
                'AttachedFile'              => $file_data
            ];
            if (!$res = $this->saveAll($save_data, ['validate' => false])) {
                return false;
            }
        }
        if ($model_type === Enum\Model\AttachedFile\AttachedModelType::TYPE_MODEL_POST) {
            /** @var PostFileService $PostFileService */
            $PostFileService = ClassRegistry::init('PostFileService');

            /** @var PostResourceService $PostResourceService */
            $PostResourceService = ClassRegistry::init('PostResourceService');

            $postFileAndAttachedFiles = $PostFileService->getPostFilesByPostId($foreign_key_id);
            foreach ($postFileAndAttachedFiles as $postFileAndAttachedFile) {
                $postFile = $postFileAndAttachedFile['PostFile'];
                $attachedFile = $postFileAndAttachedFile['AttachedFile'];

                $PostResourceService->addResourcePost(
                    $foreign_key_id,
                    $PostResourceService->getPostResourceTypeFromAttachedFileType($attachedFile['file_type']),
                    $attachedFile['id'],
                    $postFile['index_num']);
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

        /** @var PostFileService $PostFileService */
        $PostFileService = ClassRegistry::init('PostFileService');

        /** @var PostResourceService $PostResourceService */
        $PostResourceService = ClassRegistry::init('PostResourceService');

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

                if ($model_type === self::TYPE_MODEL_POST) {
                    $PostResourceService->updatePostResourceIndex($foreign_key_id, $id_or_hash, $i);
                }
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
            if (!$res = $this->saveAll($save_data, ['validate' => false])) {
                return false;
            }
        }

        if ($model_type === self::TYPE_MODEL_POST) {

            $postFileAndAttachedFiles = $PostFileService->getPostFilesByPostId($foreign_key_id);
            foreach ($postFileAndAttachedFiles as $postFileAndAttachedFile) {
                $postFile = $postFileAndAttachedFile['PostFile'];
                $attachedFile = $postFileAndAttachedFile['AttachedFile'];

                $postResourceType = $PostResourceService->getPostResourceTypeFromAttachedFileType($attachedFile['file_type']);

                if ($PostResourceService->isPostResourceExists($foreign_key_id, $attachedFile['id'],
                    $postResourceType->getValue())) {
                    continue;
                }

                $PostResourceService->addResourcePost(
                    $foreign_key_id,
                    $postResourceType,
                    $attachedFile['id'],
                    $postFile['index_num']);
            }

            foreach ($delete_files as $id) {
                $PostResourceService->deletePostResource($foreign_key_id, $id);
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
        if (empty($arr)) {
            return self::TYPE_FILE_DOC;
        }

        // 例.image/jpgだったら/の後ろのjpgのみを取得
        $subType = reset($arr);
        $fileTypes = array_combine(Hash::extract(self::$TYPE_FILE, '{n}.type'), array_keys(self::$TYPE_FILE));
        // どのタイプにも当てはまらない場合はドキュメントとして扱う
        if (!isset($fileTypes[$subType])) {
            return self::TYPE_FILE_DOC;
        }

        // 許可されていない画像の場合はドキュメントとして扱う
        $fileType = $fileTypes[$subType];
        if ($fileType == self::TYPE_FILE_IMG) {
            $allowImageTypes = Configure::read("allow_image_types");
            if (!in_array($type, $allowImageTypes)) {
                return self::TYPE_FILE_DOC;
            }
        }
        return $fileType;
    }

    /**
     * 共通のファイル削除処理(全ての紐付いた画像)
     * $model_type should be in self::$TYPE_FILE
     *
     * @param integer $foreign_key_id
     * @param integer $model_type
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

        /** @var PostResourceService $PostResourceService */
        $PostResourceService = ClassRegistry::init('PostResourceService');

        if ($model_type === self::TYPE_MODEL_POST) {
            $PostResourceService->deleteAllPostResourceByPostId($foreign_key_id);
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
     * @param int $fileId 添付ファイルのID
     * @param int $userId
     * @param int $teamId
     *
     * @return bool
     */
    public function isReadable(int $fileId, $userId = null, $teamId = null)
    {
        $file = $this->findById($fileId);
        if (empty($file)) {
            return false;
        }

        $userId = $userId ?: $this->my_uid;
        $teamId = $teamId ?: $this->current_team_id;
        // アクションに添付されたファイルは誰でも閲覧可能
        if ($file['AttachedFile']['model_type'] == self::TYPE_MODEL_ACTION_RESULT) {
            return true;
        }

        // In case of message, only topic member can download a file.
        if ($file['AttachedFile']['model_type'] == self::TYPE_MODEL_MESSAGE) {
            $modelName = self::$TYPE_MODEL[self::TYPE_MODEL_MESSAGE]['intermediateModel'];
            $row = $this->{$modelName}->find('first', [
                'conditions' => [
                    'attached_file_id' => $file['AttachedFile']['id'],
                ],
                'joins'      => [
                    [
                        'table'      => 'messages',
                        'alias'      => 'Message',
                        'type'       => 'INNER',
                        'conditions' => [
                            'MessageFile.message_id = Message.id',
                            'Message.team_id' => $teamId,
                            'Message.del_flg' => false,
                        ]
                    ],
                    [
                        'table'      => 'topic_members',
                        'alias'      => 'TopicMember',
                        'type'       => 'INNER',
                        'conditions' => [
                            'Message.topic_id = TopicMember.topic_id',
                            'TopicMember.user_id' => $userId,
                            'TopicMember.team_id' => $teamId,
                            'TopicMember.del_flg' => false,
                        ]
                    ]
                ],
            ]);
            if ($row) {
                return true;
            }
        }

        // 投稿とコメントへの添付ファイルの場合、その投稿自体が閲覧可能かを確認する。
        // アクションへのコメントの場合は、だれでも閲覧可能にする
        $postId = "";

        // 投稿への添付ファイル
        // 関連テーブルから post_id 取得
        if ($file['AttachedFile']['model_type'] == self::TYPE_MODEL_POST) {
            $modelName = self::$TYPE_MODEL[self::TYPE_MODEL_POST]['intermediateModel'];
            $row = $this->{$modelName}->find('first', [
                'conditions' => [
                    'attached_file_id' => $file['AttachedFile']['id'],
                ],
            ]);
            $postId = $row[$modelName]['post_id'];
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

            $postId = $row['Comment']['post_id'];
        }

        // 以下の場合は閲覧可能
        if (
            // 自分の投稿の場合
            $this->PostFile->Post->isMyPost($postId, $userId, $teamId) ||
            // 公開サークルに共有されている場合
            $this->PostFile->Post->PostShareCircle->isShareWithPublicCircle($postId, $teamId) ||
            // 自分が個人として共有されている場合
            $this->PostFile->Post->PostShareUser->isShareWithMe($postId, $userId, $teamId) ||
            // 自分の所属しているサークルに共有されている場合
            $this->PostFile->Post->PostShareCircle->isMyCirclePost($postId, $userId, $teamId)
        ) {
            return true;
        }

        return false;
    }

    /**
     * @param $file_id
     *
     * @return bool|null|string
     * @deprecated
     * Already this method moved to AttachedFileService.getFileUrl.
     * ファイルのURLを返す
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

    /**
     * Get first attached image each post
     * condition: attached_files.id asc
     *
     * @param int   $teamId
     * @param array $postIds
     *
     * @return array
     */
    public function findAttachedImgEachPost(int $teamId, array $postIds): array
    {
        if (empty($postIds)) {
            return [];
        }

        /** @var DboSource $db */
        $db = $this->getDataSource();
        $subQuery = $db->buildStatement([
            'fields'     => [
                'MIN(AttachedFile.id) AS id',
                'PostFile.post_id'
            ],
            'table'      => 'post_files',
            'alias'      => 'PostFile',
            'joins'      => [
                [
                    'type'       => 'INNER',
                    'table'      => 'attached_files',
                    'alias'      => 'AttachedFile',
                    'conditions' => [
                        'AttachedFile.file_type' => self::TYPE_FILE_IMG,
                        'AttachedFile.id = PostFile.attached_file_id',
                        'AttachedFile.del_flg'   => false,
                    ],
                ]
            ],
            'conditions' => [
                'PostFile.post_id' => $postIds,
                'PostFile.team_id' => $teamId,
            ],
            'group'      => 'PostFile.post_id'
        ], $this);

        $options = $this->buildCondAttachedImg($subQuery, 'post_id');
        $data = $this->find('all', $options);
        if (empty($data)) {
            return [];
        }
        $res = [];
        foreach ($data as $v) {
            $res[] = am($v['AttachedFile'], $v['AttachedFile2']);
        }
        return $res;
    }

    /**
     * Get first attached image each comment
     * condition: attached_files.id asc
     *
     * @param int   $teamId
     * @param array $commentIds
     *
     * @return array
     */
    public function findAttachedImgEachComment(int $teamId, array $commentIds): array
    {
        if (empty($commentIds)) {
            return [];
        }

        /** @var DboSource $db */
        $db = $this->getDataSource();
        $subQuery = $db->buildStatement([
            'fields'     => [
                'MIN(AttachedFile.id) AS id',
                'CommentFile.comment_id'
            ],
            'table'      => 'comment_files',
            'alias'      => 'CommentFile',
            'joins'      => [
                [
                    'type'       => 'LEFT',
                    'table'      => 'attached_files',
                    'alias'      => 'AttachedFile',
                    'conditions' => [
                        'AttachedFile.file_type' => self::TYPE_FILE_IMG,
                        'AttachedFile.id = CommentFile.attached_file_id',
                        'AttachedFile.del_flg'   => false,
                    ],
                ]
            ],
            'conditions' => [
                'CommentFile.comment_id' => $commentIds,
                'CommentFile.team_id'    => $teamId,
            ],
            'group'      => 'CommentFile.comment_id'
        ], $this);

        $options = $this->buildCondAttachedImg($subQuery, 'comment_id');
        $data = $this->find('all', $options);
        if (empty($data)) {
            return [];
        }
        $res = [];
        foreach ($data as $v) {
            $res[] = am($v['AttachedFile'], $v['AttachedFile2']);
        }
        return $res;
    }

    /**
     * Get first attached image each action
     * condition: attached_files.id asc
     *
     * @param int   $teamId
     * @param array $actionIds
     *
     * @return array
     */
    public function findAttachedImgEachAction(int $teamId, array $actionIds): array
    {
        if (empty($actionIds)) {
            return [];
        }

        /** @var DboSource $db */
        $db = $this->getDataSource();
        $subQuery = $db->buildStatement([
            'fields'     => [
                'MIN(AttachedFile.id) AS id',
                'ActionResultFile.action_result_id'
            ],
            'table'      => 'action_result_files',
            'alias'      => 'ActionResultFile',
            'joins'      => [
                [
                    'type'       => 'INNER',
                    'table'      => 'attached_files',
                    'alias'      => 'AttachedFile',
                    'conditions' => [
                        'AttachedFile.file_type' => self::TYPE_FILE_IMG,
                        'AttachedFile.id = ActionResultFile.attached_file_id',
                        'AttachedFile.del_flg'   => false,
                    ],
                ]
            ],
            'conditions' => [
                'ActionResultFile.action_result_id' => $actionIds,
                'ActionResultFile.team_id'          => $teamId,
            ],
            'group'      => 'ActionResultFile.action_result_id'
        ], $this);

        $options = $this->buildCondAttachedImg($subQuery, 'action_result_id');
        $data = $this->find('all', $options);
        if (empty($data)) {
            return [];
        }
        $res = [];
        foreach ($data as $v) {
            $res[] = am($v['AttachedFile'], $v['AttachedFile2']);
        }
        return $res;
    }

    /**
     * Build common condition for finding attached img
     *
     * @param string $subQuery
     * @param string $primaryKeyName
     *
     * @return array
     */
    private function buildCondAttachedImg(string $subQuery, string $primaryKeyName): array
    {
        $options = [
            'fields' => [
                'AttachedFile.id',
                'AttachedFile.user_id',
                'AttachedFile.team_id',
                'AttachedFile.attached_file_name',
                'AttachedFile.file_ext',
                'AttachedFile.file_size',
                'AttachedFile2.' . $primaryKeyName
            ],
            'joins'  => [
                [
                    'type'       => 'INNER',
                    'table'      => "({$subQuery})",
                    'alias'      => 'AttachedFile2',
                    'conditions' => [
                        'AttachedFile.id = AttachedFile2.id',
                        'AttachedFile.del_flg' => false,
                    ],
                ]
            ],
        ];
        return $options;
    }

    /**
     * Get list of files attached to an action result
     *
     * @param int $actionResultId
     *
     * @return AttachedFileEntity[]
     */
    public function getActionResultResources(int $actionResultId): array
    {
        $option = [
            'conditions' => [
                'AttachedFile.model_type' => Enum\Model\AttachedFile\AttachedModelType::TYPE_MODEL_ACTION_RESULT,
                'AttachedFile.del_flg'    => false
            ],
            'order'      => [
                'AttachedFile.id' => "ASC"
            ],
            'joins'      => [
                [
                    'type'       => 'INNER',
                    'table'      => 'action_result_files',
                    'alias'      => 'ActionResultFile',
                    'conditions' => [
                        'ActionResultFile.attached_file_id = AttachedFile.id',
                        'ActionResultFile.action_result_id' => $actionResultId,
                        'ActionResultFile.del_flg'          => false
                    ]
                ]
            ]
        ];

        return $this->useType()->useEntity()->find('all', $option);
    }
}

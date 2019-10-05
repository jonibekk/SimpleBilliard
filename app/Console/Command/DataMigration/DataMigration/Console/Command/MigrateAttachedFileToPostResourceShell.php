<?php
App::uses('Post', 'Model');
App::uses('PostDraft', 'Model');
App::uses('PostFile', 'Model');
App::uses('AttachedFile', 'Model');
App::uses('PostResource', 'Model');

use Goalous\Enum\Model\AttachedFile\AttachedFileType as AttachedFileType;
use Goalous\Enum\Model\Post\PostResourceType as PostResourceType;


class MigrateAttachedFileToPostResourceShell extends AppShell
{

    var $uses = array(
    );

    function startup()
    {
        parent::startup();
    }

    public function getOptionParser()
    {
        $parser = parent::getOptionParser();
        $options = [
            //'config' => ['short' => 'c', 'help' => '', 'required' => false],
        ];
        $parser->addOptions($options);
        return $parser;
    }

    public function main()
    {
        $migratedPost = 0;
        $failed = 0;
        $totalPost = $this->countPostFilesDistinct();
        $this->logInfo('Posts to migrate: ' . $totalPost);

        foreach ($this->yieldGetAllPostFiles() as list($postId, $postFileAndAttachedFiles)) {
            try {
                if ($this->saveToPostResources($postFileAndAttachedFiles)) {
                    $migratedPost++;
                }
            } catch (Exception $e) {
                $this->logError($postId . ':' . $e->getMessage());
                $failed++;
            }
            if (($migratedPost % 1000) === 0) {
                $this->logInfo('Posts migrated: ' . $migratedPost);
            }
        }
        $this->logInfo('Posts migrated: ' . $migratedPost);
        $this->logInfo('Failed migrated: ' . $failed);
    }

    /**
     * @param int $postId
     * @return int|null
     */
    private function getPostIdNextPostFile(int $postId)
    {
        /** @var PostFile $PostFile */
        $PostFile = ClassRegistry::init('PostFile');

        $condition = [
            'conditions' => [
                'PostFile.post_id > ' . $postId,
                'PostFile.del_flg'   => [0, 1],
            ],
            'fields'     => [
                'PostFile.post_id'
            ],
            'order'      => [
                'PostFile.id' => 'asc'
            ],
        ];

        $r = $PostFile->find('first', $condition);
        if (empty($r)) {
            return null;
        }
        return (int)$r['PostFile']['post_id'];
    }

    private function getPostFilesByPostId(int $postId)
    {
        /** @var PostFile $PostFile */
        $PostFile = ClassRegistry::init('PostFile');

        $condition = [
            'conditions' => [
                'PostFile.post_id' => $postId,
                'PostFile.del_flg'   => [0, 1],
            ],
            'fields' => [
                'PostFile.id',
                'PostFile.post_id',
                'PostFile.attached_file_id',
                'PostFile.team_id',
                'PostFile.index_num',
                'PostFile.del_flg',
                'PostFile.deleted',
                'PostFile.created',
                'PostFile.modified',
                'AttachedFile.id',
                'AttachedFile.user_id',
                'AttachedFile.team_id',
                'AttachedFile.attached_file_name',
                'AttachedFile.file_type',
                'AttachedFile.file_ext',
                'AttachedFile.file_size',
                'AttachedFile.model_type',
                'AttachedFile.display_file_list_flg',
                'AttachedFile.removable_flg',
                'AttachedFile.del_flg',
                'AttachedFile.deleted',
                'AttachedFile.created',
                'AttachedFile.modified',
            ],
            'joins'      => [
                [
                    'type'       => 'INNER',
                    'table'      => 'attached_files',
                    'alias'      => 'AttachedFile',
                    'conditions' => [
                        'AttachedFile.id = PostFile.attached_file_id',
                    ],
                ]
            ]
        ];

        $r = $PostFile->find('all', $condition);
        return $r;
    }

    private function saveToPostResources(array $postFileAndAttachedFiles): bool
    {
        if (empty($postFileAndAttachedFiles)) {
            throw new RuntimeException('$postFileAndAttachedFiles is empty');
        }
        /** @var PostResource $PostResource */
        $PostResource = ClassRegistry::init('PostResource');

        $postId = $postFileAndAttachedFiles[0]['PostFile']['post_id'];
        $resourceOrder = $this->getStartingOrderOfResource($postId);

        foreach ($postFileAndAttachedFiles as $postFileAndAttachedFile) {
            $postFile = $postFileAndAttachedFile['PostFile'];
            $attachedFile = $postFileAndAttachedFile['AttachedFile'];
            $attachedFileId = $attachedFile['id'];
            $resourceType = $this->resolveResourceTypeFromAttachedFileType($attachedFile['file_type']);

            // Check the data is exist, skip if exist
            if ($this->isPostResourceExists($postId, $attachedFileId, $resourceType)) {
                continue;
            }

            $PostResource->create();
            $postResource = $PostResource->save([
                'post_id' => $postId,
                'post_draft_id' => null,
                'resource_type' => $resourceType,
                'resource_id' => $postFile['attached_file_id'],
                'resource_order' => $resourceOrder,
                'del_flg' => $postFile['del_flg'],
                'deleted' => $postFile['deleted'],
                'created' => $postFile['created'],
                'modified' => $postFile['modified'],
            ]);
            $resourceOrder++;
        }
        return true;
    }

    private function isPostResourceExists(int $postId, int $attachedFileId, int $resourceType): bool
    {
        /** @var PostResource $PostResource */
        $PostResource = ClassRegistry::init('PostResource');

        $r = $PostResource->find('first', [
            'conditions' => [
                'PostResource.post_id' => $postId,
                'PostResource.resource_type' => $resourceType,
                'PostResource.resource_id' => $attachedFileId,
                'PostResource.del_flg'   => [0, 1],
            ],
            'fields'     => [
                'PostResource.id'
            ]
        ]);
        return !empty($r);
    }

    private function resolveResourceTypeFromAttachedFileType(int $attachedFileType): int
    {
        switch ($attachedFileType) {
            case AttachedFileType::TYPE_FILE_IMG:
                return PostResourceType::IMAGE;
            case AttachedFileType::TYPE_FILE_VIDEO:
                return PostResourceType::FILE_VIDEO;
            case AttachedFileType::TYPE_FILE_DOC:
                return PostResourceType::FILE;
        }
        throw new RuntimeException('Unknown AttachedFileType: ' . $attachedFileType);
    }

    private function countPostFilesDistinct(): int
    {
        /** @var PostFile $PostFile */
        $PostFile = ClassRegistry::init('PostFile');
        $r = $PostFile->find('count', [
            'conditions' => [
                'PostFile.del_flg'   => [0, 1],
            ],
            'fields' => [
                'PostFile.post_id',
            ],
            'group' => ['PostFile.post_id']
        ]);
        return (int)$r;
    }

    private function getStartingOrderOfResource($postId): int
    {
        /** @var PostResource $PostResource */
        $PostResource = ClassRegistry::init('PostResource');

        $condition = [
            'conditions' => [
                'PostResource.post_id' => $postId,
                'PostResource.del_flg'   => 0,
            ],
            'fields'     => [
                'PostResource.resource_order'
            ],
            'order'      => [
                'PostResource.resource_order' => 'desc'
            ],
        ];
        $r = $PostResource->find('first', $condition);
        if (empty($r)) {
            return 0;
        }
        return (int)$r['PostResource']['resource_order'] + 1;
    }

    /**
     * Generate returning PostFile model by yield.
     * @return Generator
     */
    private function yieldGetAllPostFiles(): Generator
    {
        $currentPostId = $this->getPostIdNextPostFile(0);
        while($currentPostId) {
            yield [$currentPostId, $this->getPostFilesByPostId($currentPostId)];
            $currentPostId = $this->getPostIdNextPostFile($currentPostId);
        }
    }


}

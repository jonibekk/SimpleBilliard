<?php
App::import('Lib/DataExtender', 'BaseExtender');
App::import('Lib/DataExtender/Extension', "UserExtension");
App::uses('AttachedFile', 'Model');
App::uses('UploadHelper', 'View/Helper');

use Goalous\Enum\Model\AttachedFile\AttachedFileType;
use \Goalous\Enum\Model\Post\PostResourceType;


class SearchPostFileExtender extends BaseExtender
{
    const EXTEND_ALL = "ext:search_post_file:all";
    const EXTEND_USER = "ext:search_post_file:user";
    const EXTEND_ATTACHED_FILE = "ext:search_post_file:attached_file";

    /** @var ImageStorageService $ImageStorageService */
    private $ImageStorageService;

    /** @var UploadHelper $UploadHelper */
    private $UploadHelper;

    function __construct()
    {
        $this->ImageStorageService = ClassRegistry::init('ImageStorageService');
        $this->UploadHelper = new UploadHelper(new View());
    }

    public function extend(array $data, int $userId, int $teamId, array $extensions = []): array
    {
        throw new RuntimeException("Please implement " . __METHOD__);
        return [];
    }

    public function extendMulti(array $data, int $userId, int $teamId, array $extensions = []): array
    {
        if ($this->includeExt($extensions, self::EXTEND_USER)) {
            /** @var UserExtension $UserExtension */
            $UserExtension = ClassRegistry::init('UserExtension');
            $data = $UserExtension->extendMulti($data, "{n}.user_id");
        }

        if($this->includeExt($extensions, self::EXTEND_ATTACHED_FILE) ) {
            foreach ($data as &$searchPostFile) {
                $attachedFileId = $searchPostFile['attached_file_id'];
                if( empty( $attachedFileId ) ) {
                    throw new \UnexpectedValueException('SearchPostFile without attached file');
                }
            
                /** @var AttachedFile $AttachedFile */
                $AttachedFile = ClassRegistry::init('AttachedFile');
                $AttachedFileData = $AttachedFile->find('first', [ 'conditions' => ['AttachedFile.id' => $attachedFileId ] ]);
                if( !$AttachedFileData ) {
                    throw new \UnexpectedValueException('SearchPostFile attached file not found');
                }

                $attachedFile =  $AttachedFileData['AttachedFile'];
                $attachedFile = $this->setupFileURLs( $attachedFile );

                $searchPostFile['attached_file'] = $attachedFile;
            }
        }

        return $data;
    }

    private function setupFileURLs( array $attachedFile ) : array {
        // Joined table does not cast types even if using useEntity()
        $attachedFile['file_type'] = (int)$attachedFile['file_type'];

        // Fetch data from attached_files
        $attachedFile['file_url'] = '';
        $attachedFile['preview_url'] = '';
        $attachedFile['download_url'] = '/posts/attached_file_download/file_id:' . $attachedFile['id'];;
        
        if ($attachedFile['file_type'] == AttachedFileType::TYPE_FILE_IMG) {
            $attachedFile['file_url'] = $this->ImageStorageService->getImgUrlEachSize($attachedFile, 'AttachedFile', 'attached');
            $attachedFile['resource_type'] = PostResourceType::IMAGE;
        } else {
            $attachedFile['preview_url'] = $this->UploadHelper->attachedFileUrl($attachedFile);
            $attachedFile['resource_type'] = PostResourceType::FILE;
        }

        return $attachedFile;
    }
}

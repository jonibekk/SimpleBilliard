<?php
App::import('Lib/DataExtender', 'BaseExtender');
App::import('Lib/DataExtender/Extension', "UserExtension");
App::uses('AttachedFile', 'Model');


class SearchPostFileExtender extends BaseExtender
{
    const EXTEND_ALL = "ext:search_post_file:all";
    const EXTEND_USER = "ext:search_post_file:user";
    const EXTEND_ATTACHED_FILE = "ext:search_post_file:attached_file";

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

                $searchPostFile['attached_file'] = $AttachedFileData['AttachedFile'];
            }
        }

        return $data;
    }
}

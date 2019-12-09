<?php

App::uses('SearchPostFile', 'Model');

/**
 * Class SearchPostFileService
 */
class SearchPostFileService extends AppService
{

    public function addAttachedFile(
        int $teamId,
        int $userId,
        int $circleId,
        int $postId,
        ?int $commentId,
        ?int $attachedFileId
    ) {
        /** @var SearchPostFile $SearchPostFile */
        $SearchPostFile = ClassRegistry::init("SearchPostFile");

        $SearchPostFile->create();
        $result = $SearchPostFile->save([
            'team_id' => $teamId,
            'user_id' => $userId,
            'circle_id' => $circleId,
            'post_id' => $postId,
            'comment_id' => $commentId,
            'attached_file_id' => $attachedFileId,
        ]);

        if( empty( $result ) ) {
            throw new \RuntimeException( 'Failed to save SearchPostFile' );
        }
    }

    /*public function removeWithPost( int $teamId, int $postId ) {

    }*/

}

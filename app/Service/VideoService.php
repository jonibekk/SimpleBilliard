<?php
App::import('Service', 'AppService');

/**
 * Class VideoService
 */
class VideoService extends AppService
{
    public function uploadToDraftPost(\SplFileInfo $fileVideo, array $user, int $teamId, array $postDraft): bool
    {
        $request = new VideoUploadRequestOnPost($fileVideo, $user, $teamId, $postDraft);
        $result = VideoStorageClient::upload($request);
        if (!$result->isSucceed()) {
            // TODO: log, show message, get error messages.
            throw new RuntimeException("failed!");
        }
        return true;
    }
}

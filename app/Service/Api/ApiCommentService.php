<?php
App::import('Service', 'AppService');
App::import('Controller/Component', 'OgpComponent');

/**
 * Class CommentService
 */
class ApiCommentService extends AppService
{
    function get($id)
    {
        if (empty($id)) {
            return [];
        }

        /** @var Comment $Comment */
        $Comment = ClassRegistry::init("Comment");

        $comment = $Comment->getComment($id);

        return $comment;
    }

    function delete($id)
    {
        /** @var Comment $Comment */
        $Comment = ClassRegistry::init("Comment");
        /** @var PostFile $PostFile */
        $PostFile = ClassRegistry::init("PostFile");

        try {
            // Validate $id
            if (empty($id) || !$Comment->exists($id)) {
                throw new NotFoundException(__("This comment doesn't exist."));
            }

            // Start transaction
            $Comment->begin();
            $Comment->delete($id);
            $PostFile->AttachedFile->deleteAllRelatedFiles($id, AttachedFile::TYPE_MODEL_COMMENT);
            $Comment->updateCounterCache(['post_id' => $id]);
            // Commit
            $Comment->commit();
        } catch (NotFoundException $e) {
            $this->log(sprintf("[%s]%s", __METHOD__, $e->getMessage()));
            $this->log($e->getTraceAsString());
            return false;
        } catch (Exception $e) {
            $this->log(sprintf("[%s]%s", __METHOD__, $e->getMessage()));
            $this->log($e->getTraceAsString());
            return false;
        }

        return true;
    }

    function update($data) {

        /** @var Comment $Comment */
        $Comment = ClassRegistry::init("Comment");
        $Comment->id = $data['id'];

        /** @var OgpComponent $OgpComponent */
        $OgpComponent = ClassRegistry::init("OgpComponent");

        // Get ogp
        $ogp = $OgpComponent->getOgpByUrlInText($data['body']);
        // Not found
        if (!isset($ogp['title'])) {
            $data['site_info'] = null;
            $data['site_photo'] = null;
        } else {
            $data['site_info'] = json_encode($ogp);
            if (isset($ogp['image'])) {
                $ext = UploadBehavior::getImgExtensionFromUrl($ogp['image']);
                if (!$ext) {
                    $ogp['image'] = null;
                }
                $data['site_photo'] = $ogp['image'];
            }
        }

        $Comment->commentEdit($data);
    }
}


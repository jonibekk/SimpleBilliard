<?php
App::import('Service', 'AppService');
App::import('Controller/Component', 'OgpComponent');

/**
 * Class CommentService
 */
class ApiCommentService extends AppService
{
    /**
     * @param $id
     *
     * @return array|null
     */
    function get($id)
    {
        if (empty($id)) {
            return [];
        }

        /** @var Comment $Comment */
        $Comment = ClassRegistry::init("Comment");

        try {
            $comment = $Comment->getComment($id);
        }
        catch (Exception $e) {
            $this->log(sprintf("[%s]%s", __METHOD__, $e->getMessage()));
            $this->log($e->getTraceAsString());
            return [];
        }
        return $comment;
    }

    function create($data)
    {
        /** @var Post $Post */
        $Post = ClassRegistry::init("Post");
        $Post->id = Hash::get($data, 'Comment.post_id');

        try {
            $Post->findById($Post->id);

            // OGP 情報を取得する URL が含まれるテキスト
            // フロントの JS でプレビューが正しく取得出来た場合は、site_info_url に URL が含まれている
            // それ以外の場合は body テキスト全体から URL を検出する
            $url_text = Hash::get($data,'Comment.site_info_url');
            if (!$url_text) {
                $url_text =  Hash::get($data, 'Comment.body');
            }

            // ogbをインサートデータに追加
            $data['Comment'] = $this->_addOgpIndexes(Hash::get($data['Comment'], 'Comment'), $url_text);

            // コメントを追加
            if (!$Post->Comment->add($data)) {
                if (!empty($this->Post->Comment->validationErrors)) {
                    $error_msg = array_shift($this->Post->Comment->validationErrors);
                    throw new RuntimeException($error_msg[0]);
                }
            }
        } catch (RuntimeException $e) {
            $this->log(sprintf("[%s]%s", __METHOD__, $e->getMessage()));
            $this->log($e->getTraceAsString());
            return false;
        }
        return $Post->Comment;
    }

    /**
     * @param $id
     *
     * Delete Comment
     *
     * @return bool
     */
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
        } catch (Exception $e) {
            $this->log(sprintf("[%s]%s", __METHOD__, $e->getMessage()));
            $this->log($e->getTraceAsString());
            return false;
        }
        return true;
    }

    /**
     * @param $data
     *
     * Update comment data
     *
     * @return bool
     */
    function update($data)
    {
        /** @var Comment $Comment */
        $Comment = ClassRegistry::init("Comment");
        $Comment->id = $data['id'];

        try {
            // Get ogp
            $data = $this->_addOgpIndexes($data, $data['body']);

            $Comment->commentEdit($data);

            // Save comment data
            $Comment->set($data);
            if ($Comment->validates()) {
                $Comment->commentEdit($data);
            } else {
                $error_msg = array_shift($Comment->validationErrors);
                throw new RuntimeException($error_msg[0]);
            }
        } catch (Exception $e) {
            $this->log(sprintf("[%s]%s", __METHOD__, $e->getMessage()));
            $this->log($e->getTraceAsString());
            return false;
        }
        return true;
    }

    /**
     * @param array  $requestData
     * @param string $body
     *
     * Add ogp to a requested object
     *
     * @return array $requestData
     */
    function _addOgpIndexes($requestData, $body)
    {
        // blank or not string, then return;
        if (!$body || !is_string($body)) {
            return $requestData;
        }

        /** @var OgpComponent $OgpComponent */
        $OgpComponent = ClassRegistry::init("OgpComponent");

        // Get OGP
        $ogp = $OgpComponent->getOgpByUrlInText($body);
        // Not found
        if (!isset($ogp['title'])) {
            $requestData['site_info'] = null;
            $requestData['site_photo'] = null;
            return $requestData;
        }

        // Set OGP data
        $requestData['site_info'] = json_encode($ogp);
        if (isset($ogp['image'])) {
            $ext = UploadBehavior::getImgExtensionFromUrl($ogp['image']);
            if (!$ext) {
                $ogp['image'] = null;
            }
            $requestData['site_photo'] = $ogp['image'];
        }
        return $requestData;
    }
}


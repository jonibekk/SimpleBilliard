<?php
App::uses('BaseApiController', 'Controller/Api');
App::uses('FileRequestValidator', 'Validator/Request/Api/V2');
App::import('Service', 'UploadService');
App::import('Service', 'AttachedFileService');
App::uses('HttpSocket', 'Network/Http');
App::import('Validator/Lib/Storage', 'UploadValidator');

/**
 * Class FilesController
 */
class FilesController extends BaseApiController
{

    public function get_download(int $fileId)
    {
        $error = $this->validateDownload($fileId);

        if (!empty($error)) {
            return $error;
        }

        /** @var AttachedFileService $AttachedFileService */
        $AttachedFileService = ClassRegistry::init("AttachedFileService");

        // Get file data and s3 url
        $url = $AttachedFileService->getFileUrl($fileId);
        $file = $AttachedFileService->get($fileId);
        $res = (new HttpSocket())->get(Router::url($url, true));
        if (!$res->body) {
            GoalousLog::error('File is not found.', [
                'fileId' => $fileId,
                'userId' => $this->getUserId(),
                'teamId' => $this->getTeamId(),
            ]);
            return ErrorResponse::notFound()->withMessage(__("This file doesn't exist."))
                ->getResponse();
        }

        return ApiResponse::ok()->getResponseForDL($res->body, $file['attached_file_name']);
    }


    /**
     * Validation method for download function
     *
     * @param int $fileId
     * @return CakeResponse|null
     */
    private function validateDownload(int $fileId)
    {
        if (!is_int($fileId)) {
            return ErrorResponse::badRequest()->getResponse();
        }

        /** @var AttachedFile $AttachedFile */
        $AttachedFile = ClassRegistry::init("AttachedFile");

        // Check if exist
        if (!$AttachedFile->exists($fileId)) {
            return ErrorResponse::notFound()->withMessage(__("This file doesn't exist."))->getResponse();
        }

        //Check if user has access to the attached file
        if (!$AttachedFile->isReadable($fileId, $this->getUserId(), $this->getTeamId())) {
            return ErrorResponse::notFound()->withMessage(__("This file doesn't exist."))
                ->getResponse();
        }

        return null;
    }

    /**
     * File uploading way is `multipart/form-data`, not `base64` in request body
     * At first, we have used `base64` way, but when upload large files, Chrome crash happens so easily.
     * The cause is not unknown still, but we decided to use `multipart/form-data` instead of `base64`
     * @return ApiResponse|BaseApiResponse|ErrorResponse
     */
    public function post_upload()
    {
        $file = Hash::get($this->request->params, 'form.file');

        $error = $this->validatePost($file);
        if (!empty($error)) {
            return $error;
        }

        /** @var UploadService $UploadService */
        $UploadService = ClassRegistry::init('UploadService');

        $content = file_get_contents($file['tmp_name']);
        $encodedFile = base64_encode($content);
        $fileName = $file['name'];

        try {
            $uuid = $UploadService->buffer($this->getUserId(), $this->getTeamId(), $encodedFile, $fileName);
        } catch (InvalidArgumentException $argumentException) {
            return ErrorResponse::badRequest()->withMessage($argumentException->getMessage())->getResponse();
        } catch (Exception $exception) {
            GoalousLog::error("Failed to upload file. " . $exception->getMessage(), $exception->getTrace());
            return ErrorResponse::internalServerError()->withException($exception)->getResponse();
        }

        /** @noinspection PhpUndefinedVariableInspection */
        return ApiResponse::ok()->withData(['file_uuid' => $uuid])->getResponse();
    }

    /**
     * Validation method for post function
     *
     * @param $file
     * @return CakeResponse|null
     */
    private function validatePost($file)
    {
        if (empty($file) || !is_array($file)) {
            return ErrorResponse::badRequest()
                ->withMessage(__('validation failed'))
                ->getResponse();
        }

        try {
            FileRequestValidator::createUploadValidator()->validate($file);
        } catch (\Respect\Validation\Exceptions\AllOfException $e) {
            return ErrorResponse::badRequest()
                ->addErrorsFromValidationException($e)
                ->withMessage(__('Failed to upload.'))
                ->getResponse();
        } catch (Exception $e) {
            GoalousLog::error('Unexpected validation exception', [
                'class' => get_class($e),
                'message' => $e,
            ]);
            return ErrorResponse::internalServerError()->getResponse();
        }

        // Check if file size doesn't over limit
        // UploadValidator do same checking, but it is after decode content.
        if ($file['size'] > UploadValidator::MAX_FILE_SIZE * 1024 * 1024) {
            return ErrorResponse::badRequest()
                ->withMessage(__("%sMB is the limit.", UploadValidator::MAX_FILE_SIZE))
                ->getResponse();
        }

        return null;
    }
}

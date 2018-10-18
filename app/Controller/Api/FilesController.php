<?php
App::uses('BaseApiController', 'Controller/Api');
App::uses('FileRequestValidator', 'Validator/Request/Api/V2');
App::import('Service', 'UploadService');
App::import('Service', 'AttachedFileService');
App::uses('HttpSocket', 'Network/Http');

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

    public function post_upload()
    {
        $error = $this->validatePost();

        if (!empty($error)) {
            return $error;
        }

        /** @var UploadService $UploadService */
        $UploadService = ClassRegistry::init('UploadService');

        $encodedFile = $this->getRequestJsonBody()['file_data'];
        $fileName = $this->getRequestJsonBody()['file_name'];

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
     * @return CakeResponse|null
     */
    private function validatePost()
    {
        $requestBody = $this->getRequestJsonBody();

        try {
            FileRequestValidator::createUploadValidator()->validate($requestBody);
        } catch (\Respect\Validation\Exceptions\AllOfException $e) {
            return ErrorResponse::badRequest()
                ->addErrorsFromValidationException($e)
                ->withMessage(__('validation failed'))
                ->getResponse();
        } catch (Exception $e) {
            GoalousLog::error('Unexpected validation exception', [
                'class' => get_class($e),
                'message' => $e,
            ]);
            return ErrorResponse::internalServerError()->getResponse();
        }

        return null;
    }
}

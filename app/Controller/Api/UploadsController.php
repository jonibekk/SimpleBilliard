<?php
App::uses('BaseApiController', 'Controller/Api');
App::import('Service', 'UploadService');

use Goalous\Exception\Upload as UploadException;

/**
 * Created by PhpStorm.
 * User: StephenRaharja
 * Date: 2018/07/24
 * Time: 14:21
 */
class UploadsController extends BaseApiController
{

    public function post()
    {
        $error = $this->validatePost();

        if (!empty($error)) {
            return $error;
        }

        /** @var UploadService $UploadService */
        $UploadService = ClassRegistry::init('UploadService');

        $encodedFile = $this->getRequestJsonBody()['file_data'];

        try {
            $uuid = $UploadService->buffer($this->getUserId(), $this->getTeamId(), $encodedFile);
        } catch (UploadException\Redis\UploadRedisLimitException $limitException) {
            return ErrorResponse::tooManyRequests()->withException($limitException)->getResponse();
        } catch (UploadException\Redis\UploadRedisSpaceException $spaceException) {
            return ErrorResponse::insufficientStorage()->withException($spaceException)->getResponse();
        } catch (UploadException\Redis\UploadRedisException $redisException) {
            GoalousLog::error("Failed to save file to Redis. " . $redisException->getMessage(),
                $redisException->getTrace());
            return ErrorResponse::internalServerError()->withException($redisException)->getResponse();
        } catch (UploadException\UploadFailedException $uploadFailedException) {
            return ErrorResponse::badRequest()->withException($uploadFailedException)->getResponse();
        } catch (InvalidArgumentException $argumentException) {
            return ErrorResponse::badRequest()->withException($argumentException)->getResponse();
        } catch (Exception $exception) {
            GoalousLog::error("Failed to upload file. " . $exception->getMessage(), $exception->getTrace());
            return ErrorResponse::internalServerError()->withException($exception)->getResponse();
        }

        /** @noinspection PhpUndefinedVariableInspection */
        return ApiResponse::ok()->withData(['file_uuid' => $uuid])->getResponse();
    }

    public function delete(string $uuid)
    {
        $error = $this->validateDelete($uuid);

        if (!empty($error)) {
            return $error;
        }

        /** @var UploadService $UploadService */
        $UploadService = ClassRegistry::init('UploadService');

        try {
            $UploadService->delete($this->getUserId(), $this->getTeamId(), $uuid);
        } catch (NotFoundException $notFoundException) {
            return ErrorResponse::notFound()->withException($notFoundException)->getResponse();
        } catch (Exception $exception) {
            GoalousLog::error("Failed to delete buffered file. " . $exception->getMessage(), $exception->getTrace());
            return ErrorResponse::internalServerError()->withException($exception)->getResponse();
        }

        return ApiResponse::ok()->getResponse();
    }

    public function delete_all()
    {
        $error = $this->validateDeleteAll();

        if (!empty($error)) {
            return $error;
        }

        /** @var UploadService $UploadService */
        $UploadService = ClassRegistry::init('UploadService');

        try {
            $UploadService->delete($this->getUserId(), $this->getTeamId());
        } catch (Exception $exception) {
            GoalousLog::error("Failed to delete buffered file. " . $exception->getMessage(), $exception->getTrace());
            return ErrorResponse::internalServerError()->withException($exception)->getResponse();
        }

        return ApiResponse::ok()->getResponse();
    }

    /**
     * Validation method for post function
     *
     * @return CakeResponse|null
     */
    private function validatePost()
    {
        return null;
    }

    /**
     * Validation method for delete function
     *
     * @param string $uuid
     *
     * @return CakeResponse|null
     */
    private function validateDelete(string $uuid = "")
    {
        if (empty($uuid) || !is_string($uuid)) {
            return ErrorResponse::badRequest()->getResponse();
        }

        return null;
    }

    /**
     * Validation method for delete function
     *
     * @return CakeResponse|null
     */
    private function validateDeleteAll()
    {
        return null;
    }
}
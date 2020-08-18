<?php
App::uses('BaseApiController', 'Controller/Api');
App::uses('FileRequestValidator', 'Validator/Request/Api/V2');
App::import('Service', 'UploadService');
App::import('Service', 'AttachedFileService');
App::uses('HttpSocket', 'Network/Http');
App::import('Validator/Lib/Storage', 'UploadValidator');
App::import('Service', 'VideoStreamService');
App::uses('UploadVideoStreamRequest', 'Service/Request/VideoStream');
App::uses('Experiment', 'Model');
App::uses('TeamConfig', 'Model');

/**
 * Class FilesController
 */

use Goalous\Exception as GlException;
use Goalous\Enum as Enum;

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
     *
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

    private function getFileSizeMaxMbUploadable(Enum\Model\Post\PostResourceType $postResourceType, int $teamId): int
    {
        if (!$postResourceType->equals(Enum\Model\Post\PostResourceType::VIDEO_STREAM())) {
            return FILE_SIZE_UPLOAD_MAX_MB;
        }

        // Upload file will upload as a video.
        /** @var VideoStreamService $VideoStreamService */
        $VideoStreamService = ClassRegistry::init('VideoStreamService');
        return $VideoStreamService->getTeamVideoFileSizeMBMax($teamId);
    }

    /**
     * File uploading way is `multipart/form-data`, not `base64` in request body
     * At first, we have used `base64` way, but when upload large files, Chrome crash happens so easily.
     * The cause is not unknown still, but we decided to use `multipart/form-data` instead of `base64`
     *
     * @return ApiResponse|BaseApiResponse|ErrorResponse
     */
    public function post_upload()
    {
        /** @var UploadService $UploadService */
        $UploadService = ClassRegistry::init('UploadService');

        $file = Hash::get($this->request->params, 'form.file');
        $allowVideo = $this->request->data('allow_video');
        $fileTypeFromFileName = $UploadService->getFileTypeFromFileName($file['name']);
        $maxUploadableFileSize = $this->getFileSizeMaxMbUploadable($fileTypeFromFileName, $this->getTeamId());

        $error = $this->validatePost($file, $maxUploadableFileSize);
        if (!empty($error)) {
            return $error;
        }

        $isVideo = $this->isVideo($file['tmp_name']);
        $fileTypeFromFileName = $UploadService->getFileTypeFromFileName($file['name']);

        // Uploading video to transcode
        if ($allowVideo && $fileTypeFromFileName->equals(Enum\Model\Post\PostResourceType::VIDEO_STREAM())) {
            // Allow posting video from front-end
            // && Frontend will decided file is video
            if (!$isVideo) {
                // && Backend not decided file is video

                // Returning error, because file type recognize is different between front/back ends.
                // TODO: When returning error, need to fix the process of recognizing file type of upload files.
                // Front/Backend recognize file type should be equal.
                // Fix Front or Backend codes, see $this->isVideo() methods about backend.
                return ErrorResponse::badRequest()->withMessage(
                    __("This video cannot be posted.")
                )->getResponse();
            }

            /** @var VideoStreamService $VideoStreamService */
            $VideoStreamService = ClassRegistry::init('VideoStreamService');
            try {
                $uploadVideoStreamRequest = new UploadVideoStreamRequest($file,
                    $this->getUserId(),
                    $this->getTeamId());
                $uploadVideoStreamRequest->setSecondsDurationLimit($VideoStreamService->getTeamVideoDurationLimit($this->getTeamId()));
                $videoStream = $VideoStreamService->uploadVideoStream($uploadVideoStreamRequest);
                GoalousLog::info('video uploaded stream', [
                    'video_streams.id' => $videoStream['id'],
                ]);
                return ApiResponse::ok()
                    ->withData([
                        "is_video" => true,
                        "video_stream_id" => $videoStream['id']
                    ])
                    ->getResponse();
            } catch (Exception $e) {
                GoalousLog::error('upload new video stream failed', [
                    'message' => $e->getMessage(),
                    'users.id' => $this->getUserId(),
                    'teams.id' => $this->getTeamId(),
                ]);
                GoalousLog::error($e->getTraceAsString());
                return ErrorResponse::badRequest()->getResponse();
            }
        }

        $content = file_get_contents($file['tmp_name']);
        $encodedFile = base64_encode($content);
        $fileName = $file['name'];

        try {
            $uuid = $UploadService->buffer($this->getUserId(), $this->getTeamId(), $encodedFile, $fileName);
        } catch (InvalidArgumentException $argumentException) {
            return ErrorResponse::badRequest()->withMessage($argumentException->getMessage())->getResponse();
        } catch (GlException\Storage\Upload\UploadValidationException $validationException) {
            return ErrorResponse::badRequest()->withMessage($validationException->getMessage())->getResponse();
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
     * @param $maxFileSizeMB
     *
     * @return BaseApiResponse|null
     */
    private function validatePost($file, int $maxFileSizeMB)
    {
        if (empty($file) || !is_array($file)) {
            return ErrorResponse::badRequest()
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
                'class'   => get_class($e),
                'message' => $e,
            ]);
            return ErrorResponse::internalServerError()->getResponse();
        }

        // Check if file size doesn't over limit
        // UploadValidator do same checking, but it is after decode content.
        if ($file['size'] > $maxFileSizeMB * 1024 * 1024) {
            return ErrorResponse::badRequest()
                ->withMessage(__("%sMB is the limit.", $maxFileSizeMB))
                ->getResponse();
        }

        return null;
    }


    /**
     * Decide the posted file is video file or not
     *
     * @param string $filePath
     * Posted file data array from 'multipart/form-data'
     * $requestFileUpload should be the
     * value get from Hash::get($this->request->params, 'form');
     *
     * @return bool
     */
    public function isVideo(string $filePath): bool
    {
        // Do not trust the ['file']['type'](= mime-type) value posted from browser
        // ['file']['type'] is resolved from only by file extension in several browser

        // TODO:
        // Investigating more certainty if the file is video or not.
        // We should use ffmpeg/ffprove

        // checking in mime-types in the file for more certain info
        if (!is_file($filePath)) {
            return false;
        }

        $fileMimeType = mime_content_type($filePath);
        $fileMimeType = strtolower($fileMimeType);
        $allowVideoTypes = Configure::read("allow_video_types");
        return in_array($fileMimeType, $allowVideoTypes);
    }
}
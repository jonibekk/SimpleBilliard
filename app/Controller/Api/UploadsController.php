<?php
App::uses('BaseApiController', 'Controller/Api');
App::uses('UploadRequestValidator', 'Validator/Request/Api/V2');
App::import('Service', 'UploadService');

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
            UploadRequestValidator::createPostValidator()->validate($requestBody);
        } catch (\Respect\Validation\Exceptions\AllOfException $e) {
            return ErrorResponse::badRequest()
                                ->addErrorsFromValidationException($e)
                                ->withMessage(__('validation failed'))
                                ->getResponse();
        } catch (Exception $e) {
            GoalousLog::error('Unexpected validation exception', [
                'class'   => get_class($e),
                'message' => $e,
            ]);
            return ErrorResponse::internalServerError()->getResponse();
        }

        return null;
    }
}
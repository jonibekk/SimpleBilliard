<?php
App::uses('BaseApiController', 'Controller/Api');
App::uses('OgpRequestValidator', 'Validator/Request/Api/V2');

use Goalous\Exception as GlException;

/**
 * Class OgpController
 * @property OgpComponent       $Ogp
 */
class OgpController extends BaseApiController
{
    public $components = [
        'Ogp',
    ];

    public function beforeFilter()
    {
        parent::beforeFilter();
    }


    /**
     * OGP のデータを取得する
     * @ignoreRestriction
     * @skipAuthentication
     * @return CakeResponse
     */
    public function get_info()
    {
        $text = $this->request->query('text');
        $error = $this->validateGetInfo($text);
        if (!empty($error)) {
            return $error;
        }

        $ogp = $this->Ogp->getOgpByUrlInText($text, true);
        return ApiResponse::ok()->withData($ogp)->getResponse();
    }


    /**
     * Validate get_info endpoint.
     *
     * @param $text
     * @return ErrorResponse | null
     */
    private function validateGetInfo($text)
    {
        try {
            OgpRequestValidator::createOgpInfoValidator()->validate(['text' => $text]);
        } catch (\Respect\Validation\Exceptions\AllOfException $e) {
            return ErrorResponse::badRequest()
                ->addErrorsFromValidationException($e)
                ->getResponse();
        } catch (Exception $e) {
            GoalousLog::error('Unexpected validation exception', [
                'class'   => get_class($e),
                'message' => $e,
            ]);
            return ErrorResponse::internalServerError()->getResponse();
        }
    }
}

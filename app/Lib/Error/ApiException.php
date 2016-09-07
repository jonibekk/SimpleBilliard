<?php

/**
 * Created by PhpStorm.
 * User: daikihirakata
 * Date: 9/7/16
 * Time: 21:48
 */
class ApiException extends HttpException
{
    public function __construct($message = null, $code = 400)
    {
        if (empty($message)) {
            $message = 'Api Error';
        }
        parent::__construct($message, $code);
    }
}

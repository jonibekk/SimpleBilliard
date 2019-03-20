<?php

/**
 * Created by PhpStorm.
 * User: bigplants
 * Date: 5/23/14
 * Time: 7:41 PM
 */
class TwoFaComponent extends CakeObject
{

    public $name = "TwoFa";

    /**
     * @var \PragmaRX\Google2FA\Google2FA $Google2Fa
     */
    public $Google2Fa = null;

    function initialize()
    {
        if (!$this->Google2Fa) {
            App::import('Vendor', 'paragmarx/google2fa/src/Google2FA');
            $this->Google2Fa = new \PragmaRX\Google2FA\Google2FA();
        }
    }

    function startup($controller)
    {
    }

    function beforeRender()
    {
    }

    function shutdown()
    {
    }

    function beforeRedirect()
    {
    }

    function generateSecretKey($length = 16)
    {
        return $this->Google2Fa->generateSecretKey($length);
    }

    function getQRCodeGoogleUrl($company, $holder, $secret)
    {
        return $this->Google2Fa->getQRCodeGoogleUrl($company, $holder, $secret);
    }

    function getQRCodeInline($company, $holder, $secret)
    {
        return $this->Google2Fa->getQRCodeInline($company, $holder, $secret);
    }

    function verifyKey($b32seed, $key, $window = 4, $useTimeStamp = true)
    {
        return $this->Google2Fa->verifyKey($b32seed, $key, $window, $useTimeStamp);
    }

}

<?php
App::uses('AppUtil', 'Util');
App::import('Lib/Cache/Redis/PaymentFlag', 'PaymentFlagClient');
App::import('Lib/Cache/Redis/PaymentFlag', 'PaymentFlagKey');
App::import('Lib/Cache/Redis/PaymentFlag', 'PaymentTiming');

use Goalous\Enum as Enum;

/**
 * set charge timing flag 
 * [Note]
 * change charge timing from user invation to user sign up.
 * chargeOnSignUp: switch
 * ChargeOnSignUpWhiteList: white list for teams 
 *
 */
class SetChargeTimingStartDateShell extends AppShell
{
    protected $allowedOPs = ['set', 'get', 'remove'];


    public function startup()
    {
        parent::startup();
        // initializing component
    }

    function getOptionParser()
    {
        $parser = parent::getOptionParser();

        $options = [
            'op' => [
                'short'    => 'o',
                'help'     => 'This is op type for the switch',
                'required' => true, // TODO: delete after set as Batch
                'choices' => $this->allowedOPs,
            ],
            'value' => [
                'short'    => 'v',
                'help'     => 'This is value for the switch',
            ],
        ];
        $parser->addOptions($options);
        return $parser;
    }

    function main()
    {
        try {
            $op = Hash::get($this->params, 'op');
            if (empty($op)) {
                $this->logError(sprintf('Shell option `%s` is not specified or not correct. %s', 'op', AppUtil::jsonOneLine([
                    'op' => $op,
                ])));
                return;
            }
            $value =  Hash::get($this->params, 'value');
            if ($op == 'set' and (empty($value))){

                $this->logError(sprintf('Shell option `%s` is not specified or not correct. %s', 'value', AppUtil::jsonOneLine([
                    'op' => $op,
                    'value' => $value,
                ])));
                return;
            }

            $paymentKeyFlagClient = new PaymentFlagClient();

            $paymentFlagKey = new PaymentFlagKey(PaymentFlagKey::SWITCH_START_DATE_NAME);
            switch ($op) {
            case 'set':
                $res = $paymentKeyFlagClient->write($paymentFlagKey, $value);
                var_dump($res);
                break;
            case 'get':
                $value = $paymentKeyFlagClient->read($paymentFlagKey);
                var_dump($value);
                break;
            case 'remove':
                $res = $paymentKeyFlagClient->del($paymentFlagKey);
                var_dump($res);
                break;
            default:
                break;
            }

        } catch (Exception $e) {
            $this->logError(sprintf("caught error on paymentKey management: %s", AppUtil::jsonOneLine([
                'message' => $e->getMessage(),
            ])));
            $this->logError($e->getTraceAsString());
        }

    }
}

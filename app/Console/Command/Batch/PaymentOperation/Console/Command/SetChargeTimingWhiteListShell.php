<?php
App::uses('AppUtil', 'Util');
App::import('Lib/Cache/Redis/PaymentFlag', 'PaymentFlagClient');
App::import('Lib/Cache/Redis/PaymentFlag', 'PaymentFlagKey');

use Goalous\Enum as Enum;

/**
 * set charge timing flag 
 * [Note]
 * change charge timing from user invation to user sign up.
 * chargeOnSignUp: switch
 * ChargeOnSignUpWhiteList: white list for teams 
 *
 */
class SetChargeTimingWhiteListShell extends AppShell
{
    protected $allowedOPs = ['set', 'get', 'delete', 'remove'];


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
            $teamIds = explode(',', $value);

            if (($op == 'set' or $op == 'delete') and (empty($teamIds) or !is_array($teamIds))){

                $this->logError(sprintf('Shell option `%s` is not specified or not correct. %s', 'value', AppUtil::jsonOneLine([
                    'op' => $op,
                    'value' => $value,
                ])));
                return;
            }

            $paymentKeyFlagClient = new PaymentFlagClient();

            $paymentFlagKey = new PaymentFlagKey(PaymentFlagKey::SWITCH_WHITE_LIST_NAME);
            switch ($op) {
            case 'set':
                $res = $paymentKeyFlagClient->writeSet($paymentFlagKey, $teamIds);
                var_dump($res);
                break;
            case 'delete':
                $res = $paymentKeyFlagClient->deleteSet($paymentFlagKey, $teamIds);
                var_dump($res);
                break;
            case 'get':
                $value = $paymentKeyFlagClient->readSet($paymentFlagKey);
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

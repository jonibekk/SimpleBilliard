<?php
App::uses('AppModel', 'Model');

/**
 * Invoice Model
 */
class Invoice extends AppModel
{
    const CREDIT_STATUS_WAITING = 0;
    const CREDIT_STATUS_OK = 1;
    const CREDIT_STATUS_NG = 2;

    public $validate = [];
}

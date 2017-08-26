<?php

App::uses('AppModel', 'Model');

class TransactionManager extends AppModel
{
    public $useTable = false;

    private static $dataSource = null;
    private static $transactionFlg = false;

    public function begin()
    {
        if (self::$transactionFlg) {
            return false;
        }

        if (is_null(self::$dataSource)) {
            self::$dataSource = $this->getDataSource();
        }

        self::$dataSource->begin($this);
        self::$transactionFlg = true;
        return true;
    }

    public function commit()
    {
        if (!self::$transactionFlg) {
            return false;
        }

        self::$dataSource->commit($this);
        self::$transactionFlg = false;
        return true;
    }

    public function rollback()
    {
        if (!self::$transactionFlg) {
            return false;
        }

        self::$dataSource->rollback($this);
        self::$transactionFlg = false;
        return true;
    }

}

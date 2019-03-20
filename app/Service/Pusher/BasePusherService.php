<?php
/**
 * Created by PhpStorm.
 * User: stephen
 * Date: 19/03/20
 * Time: 10:46
 */

abstract class BasePusherService
{
    protected $socketId;

    public function setSocketId(string $socketId)
    {
        $this->socketId = $socketId;
    }
}
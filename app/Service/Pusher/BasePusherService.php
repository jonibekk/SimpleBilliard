<?php

abstract class BasePusherService
{
    protected $socketId;

    public function setSocketId(string $socketId)
    {
        $this->socketId = $socketId;
    }
}
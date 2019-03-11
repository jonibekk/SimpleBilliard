<?php

abstract class BaseNotifiable
{
    protected $channelName = '';
    protected $eventName = '';
    protected $data = [];

    final public function getChannelName(): string
    {
        return $this->channelName;
    }

    abstract protected function setChannelName();


    final public function getEventName(): string
    {
        return $this->eventName;
    }

    abstract protected function setEventName();

    final public function getData(): array
    {
        return $this->data;
    }

    abstract protected function setData();

    abstract public function build();
}

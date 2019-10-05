<?php

abstract class BaseNotifiable
{
    protected $channelNames = [];
    protected $eventName = '';
    protected $data = [];

    final public function getChannelNames(): array
    {
        return $this->channelNames;
    }

    final public function resetChannelNames()
    {
        $this->channelNames = [];
    }

    abstract protected function setChannelNames();


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

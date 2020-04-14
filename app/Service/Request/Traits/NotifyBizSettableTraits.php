<?php

trait NotifyBizSettableTraits
{
    /**
     * @var NotifyBizComponent
     */
    protected $notifyBiz;

    /**
     * @return NotifyBizComponent
     */
    public function getNotifyBiz(): NotifyBizComponent
    {
        return $this->notifyBiz;
    }

    /**
     * @param NotifyBizComponent $notifyBiz
     */
    public function setNotifyBiz(NotifyBizComponent $notifyBiz): void
    {
        $this->notifyBiz = $notifyBiz;
    }


}

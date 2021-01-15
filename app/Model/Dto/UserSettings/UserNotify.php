<?php

/**
 * Class UserNotify
 */
class UserNotify
{
    /** @var int */
    public $id;

    /** @var int */
    public $userId;

    /** @var string */
    public $emailStatus;

    /** @var string */
    public $desktopStatus;

    /** @var string */
    public $mobileStatus;

    public function getData(): ?array
    {
        $data = array(
            'NotifySetting' => [
                'id' => $this->id,
                'user_id' => $this->userId,
                'email_status' => $this->emailStatus,
                'mobile_status' => $this->mobileStatus,
                'desktop_status' => $this->desktopStatus,
            ]
        );

        return empty($data) ? null : $data;
    }
}

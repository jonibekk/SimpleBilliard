<?php

/**
 * Class UserChangePassword
 */
class UserChangePassword
{
    /** @var int */
    public $userId;

    /** @var string */
    public $oldPassword;

    /** @var string */
    public $password;

    /** @var string */
    public $confirmPassword;

    public function getData(): ?array
    {
        $data = array(
            'User' => [
                'id' => $this->userId,
                'old_password' => $this->oldPassword,
                'password' => $this->password,
                'password_confirm' => $this->confirmPassword,
            ]
        );

        return empty($data) ? null : $data;
    }
}

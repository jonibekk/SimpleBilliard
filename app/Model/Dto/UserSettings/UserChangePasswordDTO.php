<?php

/**
 * Class UserChangePassword
 */
class UserChangePasswordDTO
{
    /** @var int */
    public $userId;

    /** @var string */
    public $oldPassword;

    /** @var string */
    public $password;

    /** @var string */
    public $confirmPassword;

    public function toArray(): ?array
    {
        $data = array(
            'id' => $this->userId,
            'old_password' => $this->oldPassword,
            'password' => $this->password,
            'password_confirm' => $this->confirmPassword,
        );

        return (empty($this->oldPassword) || empty($this->password) || empty($this->confirmPassword)) ? null : $data;
    }
}

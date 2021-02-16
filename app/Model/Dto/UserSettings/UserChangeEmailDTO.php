<?php

/**
 * Class UserChangeEmail
 */
class UserChangeEmailDTO
{
    /** @var int */
    public $userId;

    /** @var string */
    public $email;

    /** @var string */
    public $password;

    public function toArray(): ?array
    {
        $data = array(
            'email' => $this->email,
            'password_request2' => $this->password,
        );

        return (empty($this->email) || empty($this->password)) ? null : $data;
    }
}

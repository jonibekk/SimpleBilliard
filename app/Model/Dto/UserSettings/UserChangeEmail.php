<?php

/**
 * Class UserChangeEmail
 */
class UserChangeEmail
{
    /** @var int */
    public $userId;

    /** @var string */
    public $email;

    /** @var string */
    public $password;

    public function getData(): ?array
    {
        $data = array(
            'User' => [
                'email' => $this->email,
                'password_request2' => $this->password,
            ]
        );

        return empty($data) ? null : $data;
    }
}

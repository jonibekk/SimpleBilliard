<?php

/**
 * Class UserAccount
 */
class UserAccountDTO
{
    /** @var int */
    public $userId;

    /** @var string */
    public $email;

    /** @var string */
    public $defTeamId;

    /** @var string */
    public $language;

    /** @var string */
    public $timezone;

    /** @var bool */
    public $updateEmailFlag;

    /** @var string */
    public $defaultTranslationLanguage;
}

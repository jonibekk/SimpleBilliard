<?php
/**
 * Class UserSignUpFromCsv
 */
class UserSignUpFromCsv
{
    /** @var string */
    private $email;
    /** @var string */
    private $first_name;
    /** @var string */
    private $last_name;
    /** @var bool */
    private $active_flg;
    /** @var string */
    private $language;
    /** @var float */
    private $timezone;
    /** @var string */
    private $password;
    /** @var int */
    private $update_email_flg;
    /** @var int */
    private $default_team_id;
    /** @var int */
    private $agreed_terms_of_service_id;
    /** @var int */
    private $email_verified;

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @param string $email
     * @return TeamMemberBulkRegister
     */
    public function setEmail(string $email): self
    {
        $this->email = $email;
        return $this;
    }

    /**
     * @return string
     */
    public function getFirstName(): string
    {
        return $this->first_name;
    }

    /**
     * @param string $first_name
     * @return TeamMemberBulkRegister
     */
    public function setFirstName(string $first_name): self
    {
        $this->first_name = $first_name;
        return $this;
    }

    /**
     * @return string
     */
    public function getLastName(): string
    {
        return $this->last_name;
    }

    /**
     * @param string $last_name
     * @return TeamMemberBulkRegister
     */
    public function setLastName(string $last_name): self
    {
        $this->last_name = $last_name;
        return $this;
    }

    /**
     * @return bool
     */
    public function getActiveFlg(): bool
    {
        return $this->active_flg;
    }

    /**
     * @param bool $active_flg
     * @return UserSignUpFromCsv
     */
    public function setActiveFlg(bool $active_flg): self
    {
        $this->active_flg = $active_flg;
        return $this;
    }

    /**
     * @return string
     */
    public function getLanguage(): string
    {
        return $this->language;
    }

    /**
     * @param string $language
     * @return $this
     */
    public function setLanguage(string $language)
    {
        $this->language = $language;
        return $this;
    }

    /**
     * @return float
     */
    public function getTimezone(): float
    {
        return $this->timezone;
    }

    /**
     * @param float $timezone
     * @return TeamMemberBulkRegister
     */
    public function setTimezone(float $timezone): self
    {
        $this->timezone = $timezone;
        return $this;
    }

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * @param string $password
     * @return UserSignUpFromCsv
     */
    public function setPassword(string $password): self
    {
        $this->password = $password;
        return $this;
    }

    /**
     * @return int
     */
    public function getUpdateEmailFlg(): int
    {
        return $this->update_email_flg;
    }

    /**
     * @param int $update_email_flg
     * @return UserSignUpFromCsv
     */
    public function setUpdateEmailFlg(int $update_email_flg): self
    {
        $this->update_email_flg = $update_email_flg;
        return $this;
    }

    /**
     * @return int
     */
    public function getDefaultTeamId(): int
    {
        return $this->default_team_id;
    }

    /**
     * @param int $default_team_id
     * @return UserSignUpFromCsv
     */
    public function setDefaultTeamId(int $default_team_id): self
    {
        $this->default_team_id = $default_team_id;
        return $this;
    }

    /**
     * @return int
     */
    public function getAgreedTermsOfServiceId(): int
    {
        return $this->agreed_terms_of_service_id;
    }

    /**
     * @param int $agreed_terms_of_service_id
     * @return UserSignUpFromCsv
     */
    public function setAgreedTermsOfServiceId(int $agreed_terms_of_service_id): self
    {
        $this->agreed_terms_of_service_id = $agreed_terms_of_service_id;
        return $this;
    }

    /**
     * @return int
     */
    public function getEmailVerified(): int
    {
        return $this->email_verified;
    }

    /**
     * @param int $email_verified
     * @return UserSignUpFromCsv
     */
    public function setEmailVerified(int $email_verified): self
    {
        $this->email_verified = $email_verified;
        return $this;
    }
}

<?php
/**
 * Class UserSignUpFromCsv
 */
class UserSignUpFromCsv
{
    /** @var string */
    private $email;
    /** @var string */
    private $firstName;
    /** @var string */
    private $lastName;
    /** @var bool */
    private $activeFlg;
    /** @var string */
    private $language;
    /** @var float */
    private $timezone;
    /** @var string */
    private $password;
    /** @var int */
    private $updateEmailFlg;
    /** @var int */
    private $defaultTeamId;
    /** @var int */
    private $agreedTermsOfServiceId;
    /** @var int */
    private $emailVerified;

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @param string $email
     * @return UserSignUpFromCsv
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
        return $this->firstName;
    }

    /**
     * @param string $firstName
     * @return UserSignUpFromCsv
     */
    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;
        return $this;
    }

    /**
     * @return string
     */
    public function getLastName(): string
    {
        return $this->lastName;
    }

    /**
     * @param string $lastName
     * @return UserSignUpFromCsv
     */
    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;
        return $this;
    }

    /**
     * @return bool
     */
    public function getActiveFlg(): bool
    {
        return $this->activeFlg;
    }

    /**
     * @param bool $activeFlg
     * @return UserSignUpFromCsv
     */
    public function setActiveFlg(bool $activeFlg): self
    {
        $this->activeFlg = $activeFlg;
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
     * @return UserSignUpFromCsv
     */
    public function setLanguage(string $language): self
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
     * @return UserSignUpFromCsv
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
        return $this->updateEmailFlg;
    }

    /**
     * @param int $updateEmailFlg
     * @return UserSignUpFromCsv
     */
    public function setUpdateEmailFlg(int $updateEmailFlg): self
    {
        $this->updateEmailFlg = $updateEmailFlg;
        return $this;
    }

    /**
     * @return int
     */
    public function getDefaultTeamId(): int
    {
        return $this->defaultTeamId;
    }

    /**
     * @param int $defaultTeamId
     * @return UserSignUpFromCsv
     */
    public function setDefaultTeamId(int $defaultTeamId): self
    {
        $this->defaultTeamId = $defaultTeamId;
        return $this;
    }

    /**
     * @return int
     */
    public function getAgreedTermsOfServiceId(): int
    {
        return $this->agreedTermsOfServiceId;
    }

    /**
     * @param int $agreedTermsOfServiceId
     * @return UserSignUpFromCsv
     */
    public function setAgreedTermsOfServiceId(int $agreedTermsOfServiceId): self
    {
        $this->agreedTermsOfServiceId = $agreedTermsOfServiceId;
        return $this;
    }

    /**
     * @return int
     */
    public function getEmailVerified(): int
    {
        return $this->emailVerified;
    }

    /**
     * @param int $emailVerified
     * @return UserSignUpFromCsv
     */
    public function setEmailVerified(int $emailVerified): self
    {
        $this->emailVerified = $emailVerified;
        return $this;
    }
}

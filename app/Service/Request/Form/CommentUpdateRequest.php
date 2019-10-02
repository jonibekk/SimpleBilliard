<?php
App::import('Service/Request/Form', 'CommonFormRequest');

class CommentUpdateRequest extends CommonFormRequest
{
    /* @var string */
    protected $body;
    /* @var array */
    protected $siteInfo;
    /* @var array */
    protected $resources;

    /**
     * @param int $id
     * @param int $userId
     * @param int $teamId
     * @param string $body
     * @param array $siteInfo
     * @param array $resources
     */
    public function __construct(int $id, int $userId, int $teamId, string $body, array $siteInfo = [], array $resources = [])
    {
        $this->id = $id;
        $this->userId = $userId;
        $this->teamId = $teamId;
        $this->body = $body;
        $this->siteInfo = $siteInfo;
        $this->resources = $resources;
    }


    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id)
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getBody(): string
    {
        return $this->body;
    }

    /**
     * @param string $body
     */
    public function setBody(string $body)
    {
        $this->body = $body;
    }

    /**
     * @return array
     */
    public function getSiteInfo(): array
    {
        return $this->siteInfo;
    }

    /**
     * @param array $siteInfo
     */
    public function setSiteInfo(array $siteInfo)
    {
        $this->siteInfo = $siteInfo;
    }

    /**
     * @return array
     */
    public function getResources(): array
    {
        return $this->resources;
    }

    /**
     * @param array $resources
     */
    public function setResources(array $resources)
    {
        $this->resources = $resources;
    }


}

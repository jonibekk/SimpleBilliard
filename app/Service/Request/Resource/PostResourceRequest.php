<?php
App::import('Service/Request/Resource', 'CommonResourceRequest');
class PostResourceRequest extends CommonResourceRequest {
    /* @var bool */
    protected $checkPermission;

    /**
     * PostResourceRequest constructor.
     * @param int $id
     * @param int $userId
     * @param int $teamId
     * @param bool $checkPermission
     */
    public function __construct(int $id, int $userId, int $teamId, bool $checkPermission = false)
    {
        $this->id = $id;
        $this->userId = $userId;
        $this->teamId = $teamId;
        $this->checkPermission = $checkPermission;
    }

    /**
     * @return bool
     */
    public function isCheckPermission(): bool
    {
        return $this->checkPermission;
    }

    /**
     * @param bool $checkPermission
     */
    public function setCheckPermission(bool $checkPermission)
    {
        $this->checkPermission = $checkPermission;
    }

}

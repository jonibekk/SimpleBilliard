<?php
App::import('Service', 'AppService');

use Goalous\Enum as Enum;

class PusherService extends AppService
{

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Notify new post was created
     *
     * @param string $socketId
     * @param PostEntity $post
     * @param int $circleId
     */
    public function notifyNewPost(string $socketId, PostEntity $post, int $circleId = 0)
    {
        if (empty($socketId)) {
            return;
        }
        $event = "";
        $channelName = "team_" . $post['team_id'];
        switch ($post['type']) {
            case Enum\Model\Post\Type::NORMAL:
                $event = "circle_feed";
                break;
            case Enum\Model\Post\Type::ACTION:
                $event = "goal_feed";
                break;
        }

        // レスポンスデータの定義
        $notifyId = Security::hash(time());
        $data = [
            'notify_id' => $notifyId,
            'circle_id' => $circleId,
            'post_id' => $post['id']
        ];
        // push
        $pusher = $this->createInstance();
        $pusher->trigger($channelName, $event, $data, $socketId);
    }

    private function createInstance(): Pusher
    {
        return new Pusher(PUSHER_KEY, PUSHER_SECRET, PUSHER_ID);
    }
}

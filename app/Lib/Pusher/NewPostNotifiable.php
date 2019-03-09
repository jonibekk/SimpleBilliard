<?php
App::import('Lib/Pusher', 'BaseNotifiable');

use Goalous\Enum as Enum;

class NewPostNotifiable extends BaseNotifiable
{
    protected function setChannelName(int $teamId = 0)
    {
        $this->channelName =  "team_" . $teamId;
    }

    protected function setEventName(PostEntity $post = null)
    {
        switch ($post['type']) {
            case Enum\Model\Post\Type::NORMAL:
                $this->eventName = "circle_feed";
                break;
            case Enum\Model\Post\Type::ACTION:
                $this->eventName = "goal_feed";
                break;
        }
    }
    protected function setData(int $circleId = 0, PostEntity $post = null)
    {
        $notifyId = Security::hash(time());
        $this->data = [
            'notify_id' => $notifyId,
            'circle_id' => $circleId,
            'post_id' => $post['id']
        ];
    }

    public function build(PostEntity $post = null, int $circleId = 0)
    {
        $this->setChannelName($post['team_id']);
        $this->setEventName($post);
        $this->setData($circleId, $post);
    }

}

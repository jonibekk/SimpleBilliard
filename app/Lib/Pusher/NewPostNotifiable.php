<?php
App::import('Lib/Pusher', 'BaseNotifiable');

use Goalous\Enum as Enum;

class NewPostNotifiable extends BaseNotifiable
{
    protected function setChannelNames(int $teamId = 0)
    {
        $this->channelNames =  ["team_" . $teamId];
    }

    protected function setEventName(PostEntity $post = null)
    {
        switch ($post['type']) {
            case Enum\Model\Post\Type::NORMAL:
                $this->eventName = "circle_feed.new_post";
                break;
            case Enum\Model\Post\Type::ACTION:
                $this->eventName = "goal_feed.new_post";
                break;
        }
    }
    protected function setData(int $circleId = 0, PostEntity $post = null)
    {
        $this->data = [
            'circle_id' => (string)$circleId,
            'post_id' => (string)$post['id'],
            'user_id' => strval($post['user_id'])
        ];
    }

    public function build(PostEntity $post = null, int $circleId = 0)
    {
        $this->setChannelNames($post['team_id']);
        $this->setEventName($post);
        $this->setData($circleId, $post);
    }

}

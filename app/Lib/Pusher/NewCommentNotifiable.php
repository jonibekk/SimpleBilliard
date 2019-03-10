<?php
App::import('Lib/Pusher', 'BaseNotifiable');

use Goalous\Enum as Enum;

class NewCommentNotifiable extends BaseNotifiable
{
    protected function setChannelName(int $teamId = 0)
    {
        $this->channelName =  "team_" . $teamId;
    }

    protected function setEventName(int $postId = 0)
    {
        $this->eventName = "post_${postId}.new_comment";
    }
    protected function setData(int $commentId = 0, int $postId = 0)
    {
        $this->data = [
            'post_id' => (string)$postId,
            'comment_id' => (string)$commentId
        ];
    }

    public function build(int $commentId = 0, int $postId = 0, int $teamId = 0)
    {
        $this->setChannelName($teamId);
        $this->setEventName($postId);
        $this->setData($commentId, $postId);
    }

}

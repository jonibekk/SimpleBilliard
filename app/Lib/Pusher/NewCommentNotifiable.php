<?php
App::import('Lib/Pusher', 'BaseNotifiable');

use Goalous\Enum as Enum;

class NewCommentNotifiable extends BaseNotifiable
{
    protected function setChannelNames(int $teamId = 0)
    {
        $this->channelNames =  ["team_" . $teamId];
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
        $this->setChannelNames($teamId);
        $this->setEventName($postId);
        $this->setData($commentId, $postId);
    }

}

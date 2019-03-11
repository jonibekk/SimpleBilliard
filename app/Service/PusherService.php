<?php
App::import('Service', 'AppService');


class PusherService extends AppService
{
    private $pusher;
    public function __construct()
    {
        parent::__construct();
        $this->pusher = $this->createInstance();
    }

    private function createInstance(): Pusher
    {
        return new Pusher(PUSHER_KEY, PUSHER_SECRET, PUSHER_ID);
    }

    public function notify(string $socketId, BaseNotifiable $notifiable)
    {
        if (empty($socketId)) {
            GoalousLog::error('socketId is empty', [
                'notifiable' => $notifiable,
            ]);
            return;
        }
        $this->pusher->trigger($notifiable->getChannelName(), $notifiable->getEventName(), $notifiable->getData(), $socketId);
    }
}

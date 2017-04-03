<?php

/**
 * Push Message Shell
 * This shell allows you to remove cache files easily and provides you with a couple configuration options.
 * If run with no command line arguments, RemoveCache removes all your standard cache files (db cache, model cache, etc.)
 * as well as your view caching files.
 * RemoveCache Shell : Removing your Cache
 * Copyright 2009, Debuggable, Ltd. (http://debuggable.com)
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @filesource
 * @copyright     Copyright 2009, Debuggable, Ltd. (http://debuggable.com)
 * @link          http://www.cakefoundation.org/projects/info/cakephp CakePHP(tm) Project
 * @license       http://www.opensource.org/licenses/mit-license.php The MIT License
 */
class PushMessageShell extends AppShell
{

    public function getOptionParser()
    {
        $parser = parent::getOptionParser();
        $options = [
            'topicId'  => [
                'short'    => 't',
                'help'     => 'topicId',
                'required' => true,
            ],
            'socketId' => [
                'short'    => 's',
                'help'     => 'socketId. this is for excluding to push to sender. if no socketId, it will not be excluded',
                'required' => false,
            ],
        ];
        $parser->addOptions($options);
        return $parser;
    }

    public function main()
    {
        try {
            // Get params
            $topicId = Hash::get($this->params, 'topicId');
            $socketId = Hash::get($this->params, 'socketId');

            // Initialize pusher
            $pusher = new Pusher(PUSHER_KEY, PUSHER_SECRET, PUSHER_ID);
            // Send event
            $ret = $pusher->trigger('message-channel-' . $topicId, 'new_message', null, $socketId);
            if ($ret !== true) {
                throw new Exception(
                    sprintf("Failed to send event with pusher. error:%s data:%s", __METHOD__
                        , var_export($ret, true)
                        , var_export(compact('topicId', 'socketId'), true)
                    )
                );
            }
        } catch (Exception $e) {
            $this->log(sprintf("[%s]%s", __METHOD__, $e->getMessage()));
            $this->log($e->getTraceAsString());
        }
    }
}

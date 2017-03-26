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
            'topicId'  => ['short' => 't', 'help' => 'topicId', 'required' => true,],
            'socketId' => ['short' => 's', 'help' => 'socketId', 'required' => true,],
        ];
        $parser->addOptions($options);
        return $parser;
    }

    public function main()
    {
        $topicId = $this->params['topicId'];
        $socketId = $this->params['socketId'];

        $pusher = new Pusher(PUSHER_KEY, PUSHER_SECRET, PUSHER_ID);
        $pusher->trigger('message-channel-' . $topicId, 'new_message', null, $socketId);
    }
}

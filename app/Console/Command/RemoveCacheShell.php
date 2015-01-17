<?php

/**
 * Remove Cache Shell
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
class RemoveCacheShell extends AppShell
{
    public function main()
    {
        $config_list = Cache::configured();
        foreach ($config_list as $value) {
            if ($value == "session") {
                continue;
            }
            $this->out('clear ' . $value);
            Cache::clear(false, $value);
        }
        clearCache();

        $this->out('...done!');
    }
}

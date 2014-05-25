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
App::uses('Folder', 'Utility');

class RemoveCacheShell extends AppShell
{
    public $settings;

    /**
     * undocumented function
     *
     * @return void
     * @access public
     */
    function initialize()
    {
        parent::initialize();

        $this->settings = array(
            'view_cache_path' => APP . 'tmp' . DS . 'cache' . DS . 'views',
            'std_cache_paths' => array(
                APP . 'tmp',
                APP . 'tmp' . DS . 'cache',
                APP . 'tmp' . DS . 'cache' . DS . 'models',
                APP . 'tmp' . DS . 'cache' . DS . 'persistent'
            )
        );
    }

    /**
     * undocumented function
     *
     * @return void
     * @access public
     */
    function main()
    {
        $args = $this->args;

        $stdCache = !isset($args[0]) || $args[0];
        $viewCachePattern = isset($args[1]) ? $args[1] : '.*';

        //ファイル削除前にパーミッションを変更
        $tmp_folder = new Folder();
        $tmp_folder->chmod(APP . 'tmp' . DS . 'cache', 0777);
        if ($stdCache) {
            $this->_cleanStdCache();
        }

        $this->_cleanViewCache($viewCachePattern);
        //ファイル削除後にパーミッションを変更
        $tmp_folder->chmod(APP . 'tmp' . DS . 'cache', 0777);
    }

    /**
     * Cleans the standard cache, ie all model caches, db caches, persistent caches
     * Files need to be prefixed with cake_ to be removed
     *
     * @return void
     * @access public
     */
    function _cleanStdCache()
    {
        $paths = $this->settings['std_cache_paths'];

        foreach ($paths as $path) {
            $folder = new Folder($path);
            $contents = $folder->read();
            $files = $contents[1];
            $this->_fileRemove($files, $path);
            $this->_fileRemove($files, $path, 'app_');
        }
    }

    function _fileRemove($files, $path, $prefix = null)
    {
        if ($prefix) {
            $pattern = "/^" . $prefix . "/";
        }
        else {
            $pattern = "/^cake_/";
        }
        foreach ($files as $file) {
            if (!preg_match($pattern, $file)) {
                continue;
            }
            $this->out($path . DS . $file);
            @unlink($path . DS . $file);
        }
    }

    /**
     * Cleans all view caching files. Takes a pattern to match files against.
     *
     * @param string $pattern
     *
     * @return void
     * @access public
     */
    function _cleanViewCache($pattern)
    {
        $path = $this->settings['view_cache_path'];

        if ($pattern{0} != '/') {
            $pattern = '/' . $pattern . '/i';
        }

        $folder = new Folder($path);
        $contents = $folder->read(null, array(
            'empty'
        ));
        $files = $contents[1];
        foreach ($files as $file) {
            if (!preg_match($pattern, $file)) {
                continue;
            }
            $this->out($path . DS . $file);
            @unlink($path . DS . $file);
        }
    }

}

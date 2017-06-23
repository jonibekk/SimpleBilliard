<?php
App::uses('AppHelper', 'View/Helper');

/**
 * Helper class to return page specific scripts.
 *
 * Class PageResourceHelper
 */
class PageResourceHelper extends AppHelper
{
    var $helpers = array('Html');

    private $scriptMap = [
        // Scripts can be set for each action or
        // a default for the whole controller
        // Action scripts will take the preference.
        'pages' => [
            'display' => ['/js/goalous_home.min'],
            'default' => ['/js/goalous_home.min']
        ],
        'evaluations' => [
            'default' => ['/js/goalous_evaluation.min']
        ],
        'goals' => [
            'default' => ['/js/goalous_goal.min']
        ],
        // Notifications and posts uses the same base script
        // as pages (goalous_home.min)
        'notifications' => [
            'default' => ['/js/goalous_home.min']
        ],
        'posts' => [
            'default' => ['/js/goalous_home.min']
        ],
        'teams' => [
            'default' => [
                '/js/goalous_team.min',
                '/js/ng_vendors.min',
                '/js/ng_app.min']
        ],
        'users' => [
            'default' => ['/js/goalous_user.min']
        ],
    ];

    private $cssMap = [
        // Scripts can be set for each action or
        // a default for the whole controller
        // Action scripts will take the preference.
        'pages' => [
            'default' => ['feed.min']
        ],
        'evaluations' => [
            'default' => ['evaluation.min']
        ],
        'goals' => [
            'default' => ['goal_detail.min']
        ],
        // Notifications and posts uses the same base script
        // as pages (goalous_home.min)
        'notifications' => [
            'default' => ['feed.min']
        ],
        'posts' => [
            'default' => ['feed.min']
        ],
        'teams' => [
            'default' => [
                '/js/goalous_team.min',
                '/js/ng_vendors.min',
                '/js/ng_app.min']
        ],
        'users' => [
            'default' => ['user_profile.min']
        ],
    ];

    /**
     * Returns the script link tag for the page/controller
     * as specified an the map $scriptMap
     *
     * @return string|void
     */
    public function getPageScript() {

        // Requested controller
        $controller = Hash::get($this->request->params, 'controller');
        if (!isset($this->scriptMap[$controller])) {
            // No script for this controller
            return;
        }

        // Requested action
        $action = Hash::get($this->request->params, 'action');

        if (isset($this->scriptMap[$controller][$action])) {
            // Use specified script action
            return $this->_outputScripts($this->scriptMap[$controller][$action]);
        }
        else if ($this->scriptMap[$controller]['default']) {
            // User default script for the controller
            return $this->_outputScripts($this->scriptMap[$controller]['default']);
        }
    }

    /**
     * Get array of strings containing the names of script files and return
     * its html tags.
     *
     * @param array $scriptList
     *
     * @return string
     */
    private function _outputScripts(array $scriptList) {
        $out = '';
        foreach ($scriptList as $script) {
            $out .= $this->Html->script($script);
        }
        return $out;
    }

    /**
     * Returns the css link tag for the page/controller
     * as specified an the map $cssMap
     *
     * @return string
     */
    public function outputPageCss() {

        // Requested controller
        $controller = Hash::get($this->request->params, 'controller');
        if (!isset($this->cssMap[$controller])) {
            // Load page common css
            return $this->_outputCss(['page_common.min']);
        }

        // Requested action
        $action = Hash::get($this->request->params, 'action');

        $pageCssList = Hash::check($this->cssMap[$controller], $action) ?
            $this->cssMap[$controller][$action] : $this->cssMap[$controller]['default'];
        // Use specified css action
        return $this->_outputCss($pageCssList);

    }

    /**
     * Get array of strings containing the names of css files and return
     * its html tags.
     *
     * @param array $cssList
     *
     * @return string
     */
    private function _outputCss(array $cssList) {
        $out = '';
        foreach ($cssList as $css) {
            $out .= $this->Html->css($css, array('media' => 'screen'));
        }
        return $out;
    }
}

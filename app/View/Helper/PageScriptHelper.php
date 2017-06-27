<?php
App::uses('AppHelper', 'View/Helper');

/**
 * Helper class to return page specific scripts.
 *
 * Class PageScriptHelper
 */
class PageScriptHelper extends AppHelper
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
            'default'     => ['/js/goalous_goal.min'],
            'index'       => ['/js/react_goal_search_app.min'],
            'kr_progress' => ['/js/react_kr_column_app.min'],
            'create'      => ['/js/react_goal_create_app.min'],
            'edit'        => ['/js/react_goal_edit_app.min'],
            'approval'    => ['/js/react_goal_approval_app.min']
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
                '/js/ng_app.min'
            ]
        ],
        'users' => [
            'default' => ['/js/goalous_user.min']
        ],
        'setup' => [
            'default' => ['/js/react_setup_guide_app.min']
        ],
        'signup' => [
            'default' => ['/js/react_signup_app.min']
        ],
        'topics' => [
            'default' => ['/js/react_message_app.min']
        ]
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
}

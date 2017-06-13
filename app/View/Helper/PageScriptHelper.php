<?php
App::uses('AppHelper', 'View/Helper');

class PageScriptHelper extends AppHelper
{
    var $helpers = array('Html');

    private $scriptMap = [
        'pages' => [
            //'display' => ['/js/goalous_home.min'],
            'default' => ['/js/goalous_home.min']
        ],
        'evaluations' => [
            'default' => ['/js/goalous_evaluation.min']
        ],
        'goals' => [
            'default' => ['/js/goalous_goal.min']
        ],
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

    private function _outputScripts(array $scriptList) {
        $out = '';
        foreach ($scriptList as $script) {
            $out .= $this->Html->script($script);
        }
        return $out;
    }
}

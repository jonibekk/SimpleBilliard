<?php
App::uses('AppHelper', 'View/Helper');

/**
 * Helper class to return page specific scripts.
 * Class PageResourceHelper
 */
class PageResourceHelper extends AppHelper
{
    var $helpers = array('Html');

    private $scriptMap = [
        // Scripts can be set for each action or
        // a default for the whole controller
        // Action scripts will take the preference.
        'pages'         => [
            'display' => ['/js/goalous_feed.min'],
            'default' => ['/js/goalous_feed.min']
        ],
        'evaluations'   => [
            'default' => ['/js/goalous_evaluation.min']
        ],
        'goals'         => [
            'default'     => ['/js/goalous_goal.min'],
            'index'       => ['/js/react_goal_search_app.min'],
            'kr_progress' => [
                '/js/react_kr_column_app.min'
            ],
            'create'      => [
                '/js/react_goal_create_app.min'
            ],
            'edit'        => [
                '/js/react_goal_edit_app.min'
            ],
            'approval'    => [
                '/js/react_goal_approval_app.min'
            ]
        ],
        // Notifications and posts uses the same base script
        // as pages (goalous_feed.min)
        'notifications' => [
            'default' => ['/js/goalous_feed.min']
        ],
        'posts'         => [
            'default' => ['/js/goalous_feed.min']
        ],
        'teams'         => [
            'default' => [
                '/js/goalous_team.min',
            ]
        ],
        'users'         => [
            'default' => ['/js/goalous_user.min'],
            'invite'  => ['/js/react_invite_app.min']
        ],
        'setup'         => [
            'default' => [
                '/js/react_setup_guide_app.min'
            ]
        ],
        'signup'        => [
            'default' => [
                '/js/react_signup_app.min'
            ]
        ],
        'topics'        => [
            'default' => ['/js/react_message_app.min']
        ],
        'payments'      => [
            'apply'            => [
                'https://js.stripe.com/v3/',
                '/js/react_payment_apply_app.min',
                '/js/goalous_payment.min'
            ],
            'upgrade_plan'            => [
                '/js/react_campaign_plan_upgrade_app.min',
            ],
            'default'          => [],
            'update_cc_info'   => [
                'https://js.stripe.com/v3/',
                '/js/goalous_payment.min'
            ],
            'method'           => [
                '/js/goalous_payment.min',
            ],
            'contact_settings' => [
                '/js/goalous_payment.min',
            ],
            'index'            => [
                '/js/goalous_payment.min',
            ],
        ]
    ];

    private $cssMap = [
        'pages'         => [
            'default' => ['feed.min']
        ],
        'goals'         => [
            'index'      => ['goal_search.min'],
            'create'     => ['goal_create.min'],
            'edit'       => ['goal_create.min'],
            'approval'   => ['goal_approval.min'],
            'add_action' => ['feed.min'],
            'default'    => ['goal_detail.min']
        ],
        'notifications' => [
            'default' => ['feed.min']
        ],
        'posts'         => [
            'default' => ['feed.min']
        ],
        'topics'        => [
            'default' => ['topic.min'],
        ],
        'payments'      => [
            'default' => ['payments.min'],
        ],
        'setup'         => [
            'default' => ['setup_guide.min'],
        ],
        'signup'        => [
            'default' => ['signup.min'],
        ],
        'users'         => [
            'view_goals'           => ['user_profile.min'],
            'view_actions'         => ['user_profile.min'],
            'view_posts'           => ['user_profile.min'],
            'view_info'            => ['user_profile.min'],
            'register_with_invite' => ['signup.min'],
            'invite'               => ['invite.min'],
            'invite_confirm'       => ['invite.min']
        ],
        'evaluations'   => [
            'default' => ['evaluation.min'],
        ],
        'saved_items'   => [
            'default' => ['saved_item.min'],
        ],
        'teams' => [
            'main'                    => ['team_members.min', 'team_visions.min'],
            'settings'                => ['team_setting.min'],
            'invite'                  => ['signup.min'],
            'confirm_user_activation' => ['invite.min']
        ],
    ];

    /**
     * Returns the script link tag for the page/controller
     * as specified an the map $scriptMap
     *
     * @return string|void
     */
    public function getPageScript()
    {

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
        } else {
            if ($this->scriptMap[$controller]['default']) {
                // User default script for the controller
                return $this->_outputScripts($this->scriptMap[$controller]['default']);
            }
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
    private function _outputScripts(array $scriptList)
    {
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
    public function outputPageCss()
    {

        // Requested controller
        $controller = Hash::get($this->request->params, 'controller');
        if (!isset($this->cssMap[$controller])) {
            // No page css
            return '';
        }

        // Requested action
        $action = Hash::get($this->request->params, 'action');

        if (isset($this->cssMap[$controller][$action])) {
            // Use specified css action
            return $this->_outputCss($this->cssMap[$controller][$action]);
        } elseif (isset($this->cssMap[$controller]['default'])) {
            // User default css for the controller
            return $this->_outputCss($this->cssMap[$controller]['default']);
        }
        return '';
    }

    /**
     * Get array of strings containing the names of css files and return
     * its html tags.
     *
     * @param array $cssList
     *
     * @return string
     */
    private function _outputCss(array $cssList)
    {
        $out = '';
        foreach ($cssList as $css) {
            $out .= $this->Html->css($css, array('media' => 'screen'));
        }
        return $out;
    }
}

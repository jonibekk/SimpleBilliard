<?php
App::uses('AppHelper', 'View/Helper');

class BackBtnHelper extends AppHelper
{
    public $helpers = array('Session');

    public function checkPage(): bool
    {
        //Create array of pages where the normal header should appear
        $normalPages = array(
            'topics',
            'users',
            'post_permanent',
            'goals/create',
            'goals/approval/detail',
            'evaluations/view',
        );
        $backButton = true;

        foreach ($normalPages as $pageURL) {
            if (strpos($this->request->here, $pageURL) && $pageURL != 'users') {
                if ($pageURL === 'post_permanent' && !empty($this->request->query('back'))) {
                    continue;
                } else {
                    $backButton = false;
                }
            }
        }

        // Special case if arriving from notification
        if (!empty($this->request->query("notify_id"))) {
            $backButton = true;
        }

        // Special case for homepage
        if ($this->request->here == "/") {
            $backButton = false;
        }
        return $backButton;
    }
}

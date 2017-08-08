<?php
App::uses('AppHelper', 'View/Helper');

class BackBtnHelper extends AppHelper
{
    public $helpers = array('Session');
    public function checkPage() {
        //Create array of pages where the normal header should appear
        $normalPages = array(
            'topics',
            'notifications',
            'users', 
            'goals/kr_progress', 
            'post_permanent',
            'goals/create',
            'after_click:SubHeaderMenuGoal',
            'goals/approval/detail',
            'evaluations/view',
        );
        $backButton = 'true';

        foreach($normalPages as $pageURL){
            if(strpos($this->request->here , $pageURL ) && $pageURL != 'users' && $pageURL != 'post_permanent'){
                $backButton = 'false';
            } elseif ($pageURL == 'users'){
                $userUrlID = substr($this->request->here, strpos($this->request->here, ":") + 1);
                if ($userUrlID == $this->Session->read('Auth.User.id')){
                    $backButton = 'false';
                }
            } elseif ($pageURL == 'post_permanent'){
                $backButton = 'false';
            }
        }
        // Special case for homepage
        if($this->request->here == "/"){
            $backButton = 'false';
        }
        return $backButton;
    }
}
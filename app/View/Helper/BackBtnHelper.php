<?php
App::uses('AppHelper', 'View/Helper');

class BackBtnHelper extends AppHelper
{
    public function checkPage() {
        //Create array of pages where the normal header should appear
        $normalPages = array('topics','notifications','users', 'goals/kr_progress', 'post_permanent');
        $backButton = true;

        foreach($normalPages as $pageURL){
            if(strpos($this->request->here , $pageURL )){
                $backButton = false;
            }
        }
        // Special case for homepage
        if($this->request->here == "/"){
            $backButton = false;
        }
        return $backButton;
    }
}
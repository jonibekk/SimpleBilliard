<?php
App::uses('AppHelper', 'View/Helper');

class MentionHelper extends AppHelper {
    function __call($methodName, $args) {
        App::import('Component', 'Mention');
         
        $common = new MentionComponent(new ComponentCollection());
        return call_user_func_array(array($common, $methodName), $args);
    }
}
<?php
App::uses('MentionComponent', 'Controller/Component');
trait HavingMentionTrait {
    public function afterFind($results, $primary = false) {
        $hasMention = count($results) > 0;

        if ($hasMention) {
            foreach ($results as &$result) {
                if (isset($result[$this->alias]) && isset($result[$this->alias][$this->bodyProperty])) {
                    $body = $result[$this->alias][$this->bodyProperty];
                    // I know that we don't use Component in Model 
                    // but an error occurs when I use Model(s) here like 'Badge' class blah, blah, blah
                    $result[$this->alias][$this->bodyProperty] = MentionComponent::appendName($body);
                }
            }
        }
        return $results;
    }
}
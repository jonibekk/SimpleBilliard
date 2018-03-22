<?php
App::uses('MentionComponent', 'Controller/Component');
trait HavingMentionTrait {
    public function afterFind($results, $primary = false) {
        $hasMention = count($results) > 0;

        if ($hasMention) {
            foreach ($results as &$result) {
                if (isset($result[$this->alias]) && isset($result[$this->alias][$this->bodyProperty])) {
                    $body = $result[$this->alias][$this->bodyProperty];
                    $result[$this->alias][$this->bodyProperty] = MentionComponent::appendName($body);
                }
            }
        }
        return $results;
    }
}
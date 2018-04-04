<?php
App::uses('MentionComponent', 'Controller/Component');
trait HavingMentionTrait {
    public function afterFind($results, $primary = false) {
        $hasMention = count($results) > 0;
        if ($hasMention) {
            foreach ($results as &$result) {
                if (isset($result[$this->alias]) && isset($result[$this->alias][$this->bodyProperty])) {
                    $body = $result[$this->alias][$this->bodyProperty];
                    // Just comments must be manipulated for now
                    // refactor this in case of others which possibly have mentions in the future, like Post/Action
                    if (isset($result[$this->alias]['post_id'])) {
                        $accessControlledId = $result[$this->alias]['post_id'];
                        $result[$this->alias][$this->bodyProperty] = MentionComponent::appendName('Comment', $accessControlledId, $body);
                    }
                }
            }
        }
        return $results;
    }
}
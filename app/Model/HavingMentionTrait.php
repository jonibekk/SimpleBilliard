<?php
App::uses('MentionComponent', 'Controller/Component');

trait HavingMentionTrait
{
    public function afterFind($results, $primary = false)
    {
        $hasMention = count($results) > 0;
        if ($hasMention) {
            foreach ($results as &$result) {
                $result = $this->appendMentionName($result);
            }
        }

        return parent::afterFind($results, $primary);
    }

    public function afterSave($created, $options = array())
    {
        parent::afterSave($created, $options);
        $this->data = $this->appendMentionName($this->data);
    }

    final private function appendMentionName(array $data)
    {
        if (isset($data[$this->alias]) && isset($data[$this->alias][$this->bodyProperty])) {
            $body = $data[$this->alias][$this->bodyProperty];
            // Just comments must be manipulated for now
            // refactor this in case of others which possibly have mentions in the future, like Post/Action
            if ($this->alias == 'Comment') {
                if (isset($data[$this->alias]['post_id'])) {
                    $accessControlledId = $data[$this->alias]['post_id'];
                    $data[$this->alias][$this->bodyProperty] = MentionComponent::appendName('Comment',
                        $accessControlledId, $body);
                }
            }else {
                if (isset($data[$this->alias]['id'])) {
                    $accessControlledId = $data[$this->alias]['id'];
                    $data[$this->alias][$this->bodyProperty] = MentionComponent::appendName('post',
                        $accessControlledId, $body);
                }
            }
        }
        return $data;
    }
}

<?php
App::import('Model/Entity', 'BaseEntity');


class TeamTranslationLanguageEntity extends BaseEntity
{
    public function languageToArray()
    {
        return [
            'language'   => $this['language'],
            'intl_name'  => $this['intl_name'],
            'local_name' => $this['local_name']
        ];
    }
}
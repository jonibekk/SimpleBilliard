<?php
App::import('Model/Entity', 'BaseEntity');


class TranslationLanguageEntity extends BaseEntity
{
    public function toLanguageArray(): array
    {
        return [
            "language"   => $this['language'],
            'intl_name'  => __($this['intl_name']),
            'local_name' => $this['local_name']
        ];
    }
}
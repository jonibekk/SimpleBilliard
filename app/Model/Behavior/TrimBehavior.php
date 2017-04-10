<?php

/**
 * 入力値の先頭と末尾の空白文字を除去する
 */
class TrimBehavior extends ModelBehavior
{
    /**
     * 空白文字を除去しないフィールド名のリスト
     *
     * @var array
     */
    private $whiteList = ['password'];

    public function beforeSave(Model $model, $options = array())
    {
        $orig_regex_encoding = mb_regex_encoding();
        mb_regex_encoding(mb_internal_encoding());

        foreach ($model->data[$model->name] as $field => $value) {
            if (!is_string($value)) {
                continue;
            }
            if (in_array($field, $this->whiteList)) {
                continue;
            }
            $model->data[$model->name][$field] = mb_ereg_replace('(^[\s　]+)|([\s　]+$)', '', $value);
        }

        mb_regex_encoding($orig_regex_encoding);
        return true;
    }
}

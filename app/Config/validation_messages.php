<?php

/**
 * This file used on Respect/Validation rule messages
 * @see /app/Validator/BaseValidator.php::getValidationMessageFromConfig()
 */
$config['validation_messages'] = [
    'notEmpty' => __('{{field}} is a required field.'),
    'email'    => __('Email address is incorrect.'),
    'body.length' => __('{{field}} must have a length between {{minValue}} and {{maxValue}}'),
    'files.length' => __('{{field}} max limit is {{maxValue}}'),
    'resources.length' => __('{{field}} max limit is {{maxValue}}'),
    'file_ids.length' => __('{{field}} max limit is {{maxValue}}'),
];

/**
 * The translation of validation field key name.
 * Since the translation words are field name,
 * need to define both en/ja for each field.
 */
$config['translation_validation_fields'] = [
    'email' => [
        'en' => 'Email',
        'ja' => 'メールアドレス',
    ],
    'password' => [
        'en' => 'Password',
        'ja' => 'パスワード',
    ],
    'files' => [
        'en' => 'Attached file',
        'ja' => '添付ファイル',
    ],
    'resources' => [
        'en' => 'Attached file',
        'ja' => '添付ファイル',
    ],
    'file_ids' => [
        'en' => 'Attached file',
        'ja' => '添付ファイル',
    ],
    'body' => [
        'en' => 'Body',
        'ja' => '本文',
    ],
];

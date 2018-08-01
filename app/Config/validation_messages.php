<?php

/**
 * This file used on Respect/Validation rule messages
 * @see /app/Validator/BaseValidator.php::getValidationMessageFromConfig()
 */
$config['validation_messages'] = [
    'notEmpty' => __('validation.error.required'),
    'email'    => __('validation.error.email_format'),
];

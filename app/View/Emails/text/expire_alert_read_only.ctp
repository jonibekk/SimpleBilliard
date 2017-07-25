<?php
/**
 * Copyright 2010 - 2011, Cake Development Corporation (http://cakedc.com)
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright 2010 - 2011, Cake Development Corporation (http://cakedc.com)
 * @license   MIT License (http://www.opensource.org/licenses/mit-license.php)
 * @var                    $to_user_name
 * @var                    $message
 * @var                    $url
 * @var CodeCompletionView $this
 */

echo __('Hello %s.', $to_user_name);
echo "\n";
echo "\n";
echo __('The team "%s" is currently in a read-only state.', $teamName) . " " .
    __("If you would like to use regularly, subscribe to the paid plan by %s.", $expireDate);
echo "\n";
echo __("Reading-only will be canceled immediately after subscription to the paid plan.");
echo "\n";
echo __("If you do not have a subscription to the paid plan, you will not be able to use it for %s.", $expireDate);
echo "\n";
echo __("Please make a payment setting from the following link.");
echo "\n";
echo $url;
echo "\n";

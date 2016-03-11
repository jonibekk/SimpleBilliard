<?php
/**
 * Copyright 2010 - 2011, Cake Development Corporation (http://cakedc.com)
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright 2010 - 2011, Cake Development Corporation (http://cakedc.com)
 * @license   MIT License (http://www.opensource.org/licenses/mit-license.php)
 * @var                    $to_user_name
 * @var                    $url
 * @var CodeCompletionView $this
 */

echo __('Hello %s.', $to_user_name);
echo "\n";
echo "\n";
echo __('Succeeded to reset your password.');
echo "\n";
echo "\n";
echo __('If you don\'t have any idea why you get this email, it might mean someone irregularly use your email.');
echo "\n";
echo __('Please let us know as soon as possible.');
echo "\n";
echo SES_FROM_ADDRESS;
echo "\n";

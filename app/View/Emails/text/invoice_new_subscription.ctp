<?php
/**
 * Copyright 2010 - 2011, Cake Development Corporation (http://cakedc.com)
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright 2010 - 2011, Cake Development Corporation (http://cakedc.com)
 * @license   MIT License (http://www.opensource.org/licenses/mit-license.php)
 * @var                    $to_user_name
 * @var                    $teamName
 * @var                    $expireDate
 * @var                    $url
 * @var CodeCompletionView $this
 *
 *  * TODO: Create email body
 */

echo __('Hello %s.', $to_user_name);
echo "\n";
echo "\n";
echo __("Thank you for subscribing for Goalous.");
echo "\n";
echo __("Now you have access to all Goalous functionalities and support.");
echo "\n";
echo __("A monthly invoice will be sent to you in order to make payments.");
echo __("Please be advised that failing to pay these invoices will lead to interruption of Goalous service.");
echo "\n";
echo "\n";
echo __("Best regards,");
echo __("Goals team.");
echo "\n";

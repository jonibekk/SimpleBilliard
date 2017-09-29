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
 */

echo __('Hello %s.', $to_user_name) . "\n";
echo "\n";
echo __("We've attempted to bill your team, but have been unsuccessful.") . "\n";
echo __("Please check your payment method by accessing the page below:") . "\n";
echo $url . "\n";
echo "\n";
echo __("If you have any questions, please contact us.") . "\n";
echo "contact@goalous.com" . "\n";
echo "\n";
echo __("Thanks.") . "\n";
echo __("Goalous Team") . "\n";
echo "\n";


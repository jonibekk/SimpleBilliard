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

echo __('Dear %s administrator', $teamName);
echo "\n";
echo "\n";
echo __("Just a friendly reminder that the credit card (%s *******%s) is about to expire.", $brand, $lastDigits)."\n";
echo __("Please take a moment to update your card here:")."\n";
echo $url."\n";
echo "\n";
echo __("If you have any questions, please contact us.")."\n";
echo "contact@goalous.com"."\n";
echo "\n";
echo __("Thanks.")."\n";
echo __("Goalous Team")."\n";
echo "\n";

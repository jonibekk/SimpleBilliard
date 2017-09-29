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
echo __("Thank you very much for registration to Paid Plan.")."\n";
echo __("Unfortunately your organization did not pass the credit check.")."\n";
echo __("Therefore, we changed the status of your team to read-only.")."\n";
echo __("To cancel the read-only status, you need to apply to Paid Plan again with credit card.")."\n";
echo "\n";
echo __("We look forward to your continued support to Goalous in the future.")."\n";
echo "\n";

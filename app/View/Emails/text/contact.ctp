<?php
/**
 * Copyright 2010 - 2011, Cake Development Corporation (http://cakedc.com)
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright 2010 - 2011, Cake Development Corporation (http://cakedc.com)
 * @license   MIT License (http://www.opensource.org/licenses/mit-license.php)
 * @var $data
 */

echo __('Thank you for getting contact with Goalous.') . "\n";
echo "\n";
echo __('Support member will reply to you.') . "\n";
echo __('Just wait a momemt.') . "\n";
echo "\n";
echo "────────────────────\n";
echo __('What you want to ask') . "\n";
echo "────────────────────\n";
echo "- " . __('Last Name') . ":" . "\n";
echo "   " . h($data['name_last'] ?? '') . "\n";
echo "\n";
echo "\n";
echo "- " . __('First Name') . ":" . "\n";
echo "   " . h($data['name_first'] ?? '') . "\n";
echo "\n";
echo "- " . __('Your Work Email Address') . ":" . "\n";
echo "   " . h($data['email'] ?? '') . "\n";
echo "\n";
echo "- " . __('Phone Number (Optional)') . ":" . "\n";
echo "   " . h($data['phone'] ?? '') . "\n";
echo "\n";
echo "- " . __('Company Name (Optional)') . ":" . "\n";
echo "   " . h($data['company'] ?? '') . "\n";
echo "\n";

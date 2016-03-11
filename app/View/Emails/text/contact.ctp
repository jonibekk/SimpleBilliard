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
echo "\n";
echo "- " . __('Which item you want to ask') . ":" . "\n";
echo "   " . h($data['want_text']) . "\n";
echo "\n";
echo "- " . __('Your company name') . ":" . "\n";
echo "   " . h($data['company']) . "\n";
echo "\n";
echo "- " . __('Your name') . ":" . "\n";
echo "   " . h($data['name']) . "\n";
echo "\n";
echo "- " . __('Email address') . ":" . "\n";
echo "   " . h($data['email']) . "\n";
echo "\n";
echo "- " . __('What you want to ask') . ":" . "\n";
echo "   " . h($data['message']) . "\n";
echo "\n";
echo "- " . __('Sales person you appointed') . ":" . "\n";
echo "   " . h($data['sales_people_text']) . "\n";
echo "\n";

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

echo __d('email', 'このたびはGoalous（ゴーラス）へお問い合わせいただき、誠にありがとうございます。') . "\n";
echo "\n";
echo __d('email', '内容を確認のうえ、担当者より３営業日以内にご連絡いたします。') . "\n";
echo __d('email', 'どうぞよろしくお願い申し上げます。') . "\n";
echo "\n";
echo "────────────────────\n";
echo __d('email', 'お問い合わせ内容') . "\n";
echo "────────────────────\n";
echo "\n";
echo "- " . __d('email', 'お問い合わせ項目') . ":" . "\n";
echo "   " . h($data['want_text']) . "\n";
echo "\n";
echo "- " . __d('email', '会社名') . ":" . "\n";
echo "   " . h($data['company']) . "\n";
echo "\n";
echo "- " . __d('email', 'お名前') . ":" . "\n";
echo "   " . h($data['name']) . "\n";
echo "\n";
echo "- " . __d('email', 'メールアドレス') . ":" . "\n";
echo "   " . h($data['email']) . "\n";
echo "\n";
echo "- " . __d('email', 'お問い合わせ内容') . ":" . "\n";
echo "   " . h($data['message']) . "\n";
echo "\n";
echo "- " . __d('email', 'ご希望の営業担当者') . ":" . "\n";
echo "   " . h($data['sales_people_text']) . "\n";
echo "\n";

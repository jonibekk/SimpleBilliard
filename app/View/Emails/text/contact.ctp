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

echo __d('mail', 'このたびはGoalous（ゴーラス）へお問い合わせいただき、誠にありがとうございます。') . "\n";
echo "\n";
echo __d('mail', '内容を確認のうえ、担当者より３営業日以内にご連絡いたします。') . "\n";
echo __d('mail', 'どうぞよろしくお願い申し上げます。') . "\n";
echo "\n";
echo "────────────────────\n";
echo __d('mail', 'お問い合わせ内容') . "\n";
echo "────────────────────\n";
echo "\n";
echo "  - " . __d('mail', 'お問い合わせ項目') . ": " . h($data['want_text']) . "\n";
echo "  - " . __d('mail', '会社名') . ": " . h($data['company']) . "\n";
echo "  - " . __d('mail', 'お名前') . ": " . h($data['name']) . "\n";
echo "  - " . __d('mail', 'メールアドレス') . ": " . h($data['email']) . "\n";
echo "  - " . __d('mail', 'お問い合わせ内容') . ": " . h($data['message']) . "\n";
echo "  - " . __d('mail', 'ご希望の営業担当者') . ": " . h($data['sales_people_text']) . "\n";
echo "\n";
echo __d('mail', '■ご注意') . "\n";
echo __d('mail', 'このメールは、お問い合わせメールアドレス宛に自動的に送信されています。') . "\n";
echo __d('mail', '直接返信されないようお願い申し上げます。') . "\n";
echo "\n";
echo __d('mail', 'このメールに心当たりの無い方は、お問い合わせフォームからお問い合わせください。') . "\n";
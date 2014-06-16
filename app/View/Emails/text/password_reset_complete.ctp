<?php
/**
 * Copyright 2010 - 2011, Cake Development Corporation (http://cakedc.com)
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright 2010 - 2011, Cake Development Corporation (http://cakedc.com)
 * @license   MIT License (http://www.opensource.org/licenses/mit-license.php)
 * @var $to_user_name
 * @var $url
 */

echo __d('mail', 'こんにちは %sさん、', $to_user_name);
echo "\n";
echo "\n";
echo __d('mail', 'パスワードの再設定が完了しました。');
echo "\n";
echo "\n";
echo __d('mail', 'もし、このメールに心当たりがない場合は、第三者が不正にあなたのパスワードを利用してログインしている可能性があります。');
echo "\n";
echo __d('mail', 'その場合は直ちに以下にご連絡ください。');
echo "\n";
echo SES_FROM_ADDRESS;
echo "\n";

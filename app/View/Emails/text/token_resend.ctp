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

echo __d('mail', 'こんにちは %sさん、', $to_user_name);
echo "\n";
echo __d('mail', 'Goalousへようこそ！');
echo "\n";
echo "\n";
echo __d('mail', '以下のリンクをクリックしてメールアドレスの認証を行ってください。');
echo "\n";
echo $url;
echo "\n";
echo "\n";
echo "\n";

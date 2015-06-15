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
echo "\n";
echo __d('mail', '以下のリンクをクリックしてパスワードを再設定してください。');
echo "\n";
echo $url;
echo "\n";
echo "\n";
echo __d('mail', 'もし、このメールに心当たりがない場合は、何もせずにメールを破棄してください。');
echo "\n";

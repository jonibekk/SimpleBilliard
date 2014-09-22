<?php
/**
 * Copyright 2010 - 2011, Cake Development Corporation (http://cakedc.com)
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright 2010 - 2011, Cake Development Corporation (http://cakedc.com)
 * @license   MIT License (http://www.opensource.org/licenses/mit-license.php)
 * @var                    $to_user_name
 * @var                    $from_user_name
 * @var                    $body
 * @var                    $body_title
 * @var                    $url
 * @var CodeCompletionView $this
 */
if ($to_user_name) {
    echo __d('mail', 'こんにちは %sさん、', h($to_user_name));
}
else {
    echo __d('mail', 'こんにちは。');

};
echo "\n";
echo "\n";
echo $body_title;
echo "\n";
echo __d('mail', '「%s」', $this->TextEx->replaceUrl($body[0]));
echo "\n";
echo "\n";
echo __d('mail', '以下のリンクから内容の確認ができます。');
echo "\n";
echo $url;
echo "\n";
echo "\n";
echo "\n";

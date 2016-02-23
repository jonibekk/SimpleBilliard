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
 * @var                    $team_name
 * @var                    $message
 * @var                    $url
 * @var CodeCompletionView $this
 */
if ($to_user_name) {
    echo __('こんにちは %sさん、', h($to_user_name));
}
else {
    echo __('こんにちは。');

};
echo "\n";
echo "\n";
echo __('%sさんがGoalous内のチーム「%s」へあなたを招待しています。', h($from_user_name), h($team_name));
echo "\n";
echo "\n";
echo __('この招待に応じる場合は、次のリンクをクリックしてください。');
echo "\n";
echo $url;
echo "\n";
echo "\n";
if ($message) {
    echo __("%sさんからのメッセージ：", h($from_user_name));
    echo "\n";
    echo h($message);
    echo "\n";
    echo "\n";
}
echo "\n";

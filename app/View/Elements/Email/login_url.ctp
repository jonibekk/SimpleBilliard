<?php
echo "━━━━━━━━━━━━━━━━━━━━";
echo "\n";
echo "\n";
echo __d('mail', '◆Goalousログイン');
echo "\n";
echo "->";
if (isset($vol) && !empty($vol)) {
    echo Router::url('/users/login/?g=' . $vol, true);
}
else {
    echo Router::url(array(
                         'controller' => 'users',
                         'action'     => 'login'
                     ), true);
}
echo "\n";
echo "\n";

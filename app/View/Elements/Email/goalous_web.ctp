<?php
if (isset($vol) && !empty($vol)) {
    //jpnの場合
    if (isset($user['User']['language']) && $user['User']['language'] == "jpn") {
        $blog_param = "?lang=ja&g=" . $vol;
    }
    else {
        $blog_param = "?g=" . $vol;
    }
    $gl_param = "?g=" . $vol;
}
else {
    $blog_param = null;
    $gl_param = null;
}
echo "━━━━━━━━━━━━━━━━━━━━";
echo "\n";
echo __d('mail', '◆Follow on Facebook');
echo "\n";
echo "-> http://www.facebook.com/goalous";
echo "\n";
echo __d('mail', '◆Goalous Blog');
echo "\n";
echo "-> http://blog.goalous.com/" . $blog_param;
echo "\n";
echo __d('mail', '◆Goalousホーム');
echo "\n";
echo "-> ";
echo Router::url('/' . $gl_param, true);
echo "\n";

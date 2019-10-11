<?php
echo __('Hello!') . "\n";
echo "\n";
echo __('You have a new invitation from the %s team in Goalous.', $teamName) . "\n";
echo "\n";
if ($password !== null) {
    echo __('You have been invited to the team. Please use username and password given below to login in the following URL.') . "\n";
    echo "\n";
    echo __('* This is a temporary password, so you should change it immediately. Click More(in Header) > User Settings >  Account > Password > Change Password, you can change the password.') . "\n";
    echo "\n";
    echo __('Email Address: %s', $email) . "\n";
    echo __('Password') . ': ' . $password . "\n";
    echo "\n";
} else {
    echo __('You have been invited to the team. Please use your username and password to login in the following URL.') . "\n";
    echo "\n";
}
echo __('【Log in URL】') . "\n";
echo $url . "\n";
echo "\n";
echo "\n";

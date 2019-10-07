<?php
echo __('Hello!') . "\n";
echo "\n";
echo __('You have a new invitation from the %s team in Goalous.', $team_name) . "\n";
echo "\n";
echo __('You have been invited to the team. Please use username and password given below to login in the following URL.') . "\n";
echo "\n";
if ($password !== null) {
    echo __('Email Address: %s', $email) . "\n";
    echo __('Password') . ': ' . $password . "\n";
    echo "\n";
}
echo __('【Log in URL】') . "\n";
echo $url . "\n";
echo "\n";
echo "\n";

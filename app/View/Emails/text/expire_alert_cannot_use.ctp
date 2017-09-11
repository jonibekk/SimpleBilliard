<?php
/**
 * Copyright 2010 - 2011, Cake Development Corporation (http://cakedc.com)
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright 2010 - 2011, Cake Development Corporation (http://cakedc.com)
 * @license   MIT License (http://www.opensource.org/licenses/mit-license.php)
 * @var                    $to_user_name
 * @var                    $teamName
 * @var                    $expireDate
 * @var                    $url
 * @var CodeCompletionView $this
 */

echo __('Hello %s.', $to_user_name);
echo "\n";
echo "\n";
echo __("The team \"%s\" is currently unavailable.", $teamName) . " " .
    __("If you would like to restore your team to a regular status, please subscribe to the paid plan by %s.",
        $this->TimeEx->formatDateI18nFromDate($expireDate)
    );
echo "\n";
echo __("Your team will immediately be able to resume normal use after subscribing to the paid plan.");
echo "\n";
echo __("If you do not subscribe to the paid plan by %s, all of your team's data will be deleted.",
    $this->TimeEx->formatDateI18nFromDate($expireDate)
);
echo "\n";
echo __("You can update your payment setting from the following link:");
echo "\n";
echo $url;
echo "\n";

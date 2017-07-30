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
echo __("The free trial for your team, \"%s\", will end on %s",
        $teamName,
        $this->TimeEx->formatDateI18nFromDate($expireDate)
    ) . " " . __("If you would like to continue using Goalous, please subscribe to the paid plan.");
echo "\n";
echo __("You can update your payment setting from the following link:");
echo "\n";
echo $url;
echo "\n";

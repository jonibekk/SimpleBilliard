<?php

/**
 * Class PaymentUtil
 */
class TextUtil
{
    static function extractAllIdFromMention($text) {
        preg_match_all('/<@(.*?)>/m', $text, $matches);
        $result = array();
        if (count($matches[1]) > 0) {
	        foreach ($matches[1] as $match) {
	        	$isUser = strpos($match, 'user') === 0;
	        	$isCircle = strpos($match, 'circle') === 0;
	        	$replacement = '';
	        	if ($isUser) {
	        		$replacement = 'user_';
	        	}else if ($isCircle) {
	        		$replacement = 'circle_';
	        	}
	        	$result[$match] = array(
	        		'id' => str_replace($replacement, '', $match),
	        		'isUser' => strpos($match, 'user') === 0, 
	        		'isCircle' => strpos($match, 'circle') === 0
	        	);
	        }
	    }
        return $result;
    }

    static function replaceAndAddNameToMention($pattern, $replacement, $subject) {
        $result = preg_replace('/<@'.$pattern.'>/m', '<@'.$pattern.':'.$replacement.'>', $subject);
        return $result;
    }

}

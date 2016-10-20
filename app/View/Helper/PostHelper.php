<?php
/**
 * Created by PhpStorm.
 * User: saekis
 * Date: 16/03/16
 * Time: 18:08
 */

App::uses('AppHelper', 'View/Helper');

class PostHelper extends AppHelper
{

    /**
     * @param $json_site_info
     *
     * @return string
     */
    function extractOgpUrl($json_site_info)
    {
        if (!$json_site_info) {
            return '';
        }

        $site_info = json_decode($json_site_info);
        if (Hash::get($site_info->url)) {
            return $site_info->url;
        }

        return '';
    }

}

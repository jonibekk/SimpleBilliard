<?php

/**
 * Created by PhpStorm.
 * User: daikihirakata
 * Date: 2014/05/28
 * Time: 0:36
 */

/** @noinspection PhpUndefinedClassInspection */
class OgpComponent extends CakeObject
{

    public $name = "Ogp";

    /**
     * @var AppController $Controller
     */
    var $Controller;

    function initialize($controller)
    {
        $this->Controller = $controller;
    }

    function startup()
    {

    }

    function beforeRender()
    {
    }

    function shutdown()
    {
    }

    function beforeRedirect()
    {
    }

    /**
     * There are base schema's based on type, this is just
     * a map so that the schema can be obtained
     */
    public static $TYPES = array(
        'activity'     => array('activity', 'sport'),
        'business'     => array('bar', 'company', 'cafe', 'hotel', 'restaurant'),
        'group'        => array('cause', 'sports_league', 'sports_team'),
        'organization' => array('band', 'government', 'non_profit', 'school', 'university'),
        'person'       => array('actor', 'athlete', 'author', 'director', 'musician', 'politician', 'public_figure'),
        'place'        => array('city', 'country', 'landmark', 'state_province'),
        'product'      => array('album', 'book', 'drink', 'food', 'game', 'movie', 'product', 'song', 'tv_show'),
        'website'      => array('blog', 'website'),
    );

    /**
     * Holds all the Open Graph values we've parsed from a page
     */
    private $_values = array();

    /**
     * @param $text
     *
     * @return array|null
     */
    public function getOgpByUrlInText($text)
    {
        preg_match_all('(https?://[-_.!~*\'()a-zA-Z0-9;/?:@&=+$,%#]+)', $text, $urls);
        //一番目のurlを取り出す。
        if (!empty($urls[0][0])) {
            // 内部 OPG のチェック
            $ogp = $this->getInternalOgpByUrl($urls[0][0]);
            if ($ogp) {
                return $ogp;
            }

            // 外部 OGP のチェック
            $ogp = $this->getOgpByUrl($urls[0][0]);
            return $ogp;
        }
        return null;
    }

    public function getOgpByUrl($url)
    {
        $ogp = $this->fetch($url);
        $res = [];
        if (isset($ogp->title)) {
            $res['title'] = $ogp->title;

        }
        if (isset($ogp->description)) {
            $res['description'] = $ogp->description;
        }
        if (isset($ogp->url)) {
            $res['url'] = $ogp->url;
        } else {
            $res['url'] = $url;
        }
        $res['type'] = 'external';
        if (isset($ogp->image)) {
            //imageのurlにホストが含まれているかチェックし、
            //含まれていなければ含める
            $image_url_array = parse_url($ogp->image);
            if (!isset($image_url_array['scheme']) &&
                !isset($image_url_array['host'])
            ) {
                $url_array = parse_url($url);
                if (isset($url_array['scheme']) &&
                    isset($url_array['host'])
                ) {
                    $image_url_array['scheme'] = $url_array['scheme'];
                    $image_url_array['host'] = $url_array['host'];
                    $image_url = $image_url_array['scheme'];
                    $image_url .= "://";
                    $image_url .= $image_url_array['host'];
                    $image_url .= $image_url_array['path'];

                    $ogp->image = $image_url;
                } else {
                    $ogp->image = null;
                }
            } //urlの文字列が//で始まっていた場合
            elseif (strpos($ogp->image, '//') === 0) {
                $ogp->image = 'http:' . $ogp->image;
            }
            $res['image'] = $ogp->image;
        }
        if (isset($ogp->site_name)) {
            $res['site_name'] = $ogp->site_name;
        }
        if (isset($ogp->site_url)) {
            $res['site_url'] = $ogp->site_url;
        }

        // in case of no data, just add the title same as the domain name
        if (!isset($ogp->title)) {
            $url_detail = parse_url($url);
            $res['title'] = $url_detail['host'];

        }

        return $res;
    }

    /**
     * $url が自サイトのドメインで、内部 OGP の対象の場合は情報を返す
     *
     * @param $url
     *
     * @return array
     */
    public function getInternalOgpByUrl($url)
    {
        // ドメインチェック
        if (strpos($url, Router::fullBaseUrl()) !== 0) {
            return [];
        }

        App::uses('UploadHelper', 'View/Helper');
        $Upload = new UploadHelper(new View());
        $ogp = [];
        $url_info = Router::parse(str_replace(Router::fullBaseUrl(), '', $url));
        if ($url_info['controller'] == 'posts' && $url_info['action'] == 'feed') {
            // 投稿単体ページ
            if (isset($url_info['post_id'])) {
                $posts = ClassRegistry::init('Post')->get(1, 1, null, null, ['post_id' => $url_info['post_id']]);
                if ($posts) {
                    $post = $posts[0];

                    $attached_files = [];
                    switch ($post['Post']['type']) {
                        // 通常投稿
                        case Post::TYPE_NORMAL:
                            $ogp['type'] = 'post_normal';
                            $ogp['title'] = explode("\n", ltrim($post['Post']['body']))[0];
                            $ogp['description'] = $post['User']['roman_username'];
                            $attached_files = $post['PostFile'];
                            break;

                        // ゴール作成
                        case Post::TYPE_CREATE_GOAL:
                            $ogp['type'] = 'post_create_goal';
                            $ogp['title'] = $post['Goal']['name'];
                            $ogp['description'] = Post::$TYPE_MESSAGE[Post::TYPE_CREATE_GOAL];
                            $ogp['image'] = Router::url($Upload->uploadUrl($post, "Goal.photo",
                                ['style' => 'large']), true);
                            break;

                        // アクション
                        case Post::TYPE_ACTION:
                            $ogp['type'] = 'post_action';
                            $ogp['title'] = explode("\n", ltrim($post['ActionResult']['name']))[0];
                            $ogp['description'] = $post['User']['roman_username'];
                            $attached_files = $post['ActionResult']['ActionResultFile'];
                            break;

                        // KR達成
                        case Post::TYPE_KR_COMPLETE:
                            $ogp['type'] = 'post_kr_complete';
                            $ogp['title'] = $post['Goal']['name'];
                            $ogp['description'] = __("Achieved %s!", $post['KeyResult']['name']);
                            $ogp['image'] = Router::url($Upload->uploadUrl($post, "Goal.photo",
                                ['style' => 'large']), true);
                            break;

                        // ゴール達成
                        case Post::TYPE_GOAL_COMPLETE:
                            $ogp['type'] = 'post_goal_complete';
                            $ogp['title'] = $post['Goal']['name'];
                            $ogp['description'] = __("Achieved a Goal.");
                            $ogp['image'] = Router::url($Upload->uploadUrl($post, "Goal.photo",
                                ['style' => 'large']), true);
                            break;

                        // サークル作成
                        case Post::TYPE_CREATE_CIRCLE:
                            $ogp['type'] = 'post_create_circle';
                            $ogp['title'] = $post['Circle']['name'];
                            $ogp['description'] = Post::$TYPE_MESSAGE[Post::TYPE_CREATE_CIRCLE];
                            $ogp['image'] = Router::url($Upload->uploadUrl($post, "Circle.photo",
                                ['style' => 'large']), true);
                            break;
                    }

                    $ogp['site_name'] = $ogp['title'];
                    $ogp['url'] = $url;

                    // 添付ファイルに画像があれば追加
                    foreach ($attached_files as $f) {
                        if (in_array(strtolower($f['AttachedFile']['file_ext']), ['jpg', 'jpeg', 'png', 'gif'])) {
                            $ogp['image'] = Router::url($Upload->uploadUrl($f, "AttachedFile.attached",
                                ['style' => 'large']), true);
                            break;
                        }
                    }
                    // ユーザーのローカル名を全て保存
                    $ogp['user_local_names'] = $this->_getUserLocalNames($post['User']['id']);
                }
            } // サークルページ
            elseif (isset($url_info['circle_id'])) {
                $circle = ClassRegistry::init('Circle')->findById($url_info['circle_id']);
                if ($circle) {
                    $ogp['type'] = 'circle';
                    $ogp['title'] = $circle['Circle']['name'];
                    $ogp['description'] = $circle['Circle']['description'];
                    $ogp['url'] = $url;
                    $ogp['image'] = Router::url($Upload->uploadUrl($circle, 'Circle.photo',
                        ['style' => 'medium_large']), true);
                    $ogp['site_name'] = $circle['Circle']['name'];
                }
            }
        } elseif ($url_info['controller'] == 'teams' && $url_info['action'] == 'main#') {
            // チームビジョン
            if ($url_info['pass'][0] == 'vision_detail') {
                $team_vision = ClassRegistry::init('TeamVision')->findById($url_info['pass'][1]);
                if ($team_vision) {
                    $ogp['type'] = 'team_vision';
                    $ogp['title'] = $team_vision['TeamVision']['name'];
                    $ogp['description'] = $team_vision['TeamVision']['description'];
                    $ogp['url'] = $url;
                    $ogp['image'] = Router::url($Upload->uploadUrl($team_vision, 'TeamVision.photo',
                        ['style' => 'medium_large']), true);
                    $ogp['site_name'] = $team_vision['TeamVision']['name'];
                }
            } // グループビジョン
            elseif ($url_info['pass'][0] == 'group_vision_detail') {
                $group_vision = ClassRegistry::init('GroupVision')->findById($url_info['pass'][1]);
                if ($group_vision) {
                    $ogp['type'] = 'group_vision';
                    $ogp['title'] = $group_vision['GroupVision']['name'];
                    $ogp['description'] = $group_vision['GroupVision']['description'];
                    $ogp['url'] = $url;
                    $ogp['image'] = Router::url($Upload->uploadUrl($group_vision, 'GroupVision.photo',
                        ['style' => 'medium_large']), true);
                    $ogp['site_name'] = $group_vision['GroupVision']['name'];
                }
            }
        } // ユーザーマイページ
        elseif (
            ($url_info['controller'] == 'users' && $url_info['action'] == 'view_goals') ||
            ($url_info['controller'] == 'users' && $url_info['action'] == 'view_actions') ||
            ($url_info['controller'] == 'users' && $url_info['action'] == 'view_posts') ||
            ($url_info['controller'] == 'users' && $url_info['action'] == 'view_krs')
        ) {
            $user = ClassRegistry::init('TeamMember')->getByUserId($url_info['named']['user_id']);
            if ($user) {
                $ogp['type'] = 'user';
                $ogp['title'] = $user['User']['roman_username'];
                $ogp['description'] = $user['TeamMember']['comment'];
                $ogp['url'] = $url;
                $ogp['image'] = Router::url($Upload->uploadUrl($user, 'User.photo',
                    ['style' => 'medium_large']), true);
                $ogp['site_name'] = $user['User']['roman_username'];
                // ユーザーのローカル名を全て保存
                $ogp['user_local_names'] = $this->_getUserLocalNames($user['User']['id']);
            }
        } // ゴールページ
        elseif (
            ($url_info['controller'] == 'goals' && $url_info['action'] == 'view_krs') ||
            ($url_info['controller'] == 'goals' && $url_info['action'] == 'view_krs') ||
            ($url_info['controller'] == 'goals' && $url_info['action'] == 'view_actions') ||
            ($url_info['controller'] == 'goals' && $url_info['action'] == 'view_members') ||
            ($url_info['controller'] == 'goals' && $url_info['action'] == 'view_followers')
        ) {
            $goal = ClassRegistry::init('Goal')->findById($url_info['named']['goal_id']);
            if ($goal) {
                $ogp['type'] = 'goal';
                $ogp['title'] = $goal['Goal']['name'];
                $ogp['description'] = $goal['Goal']['description'];
                $ogp['url'] = $url;
                $ogp['image'] = Router::url($Upload->uploadUrl($goal, 'Goal.photo',
                    ['style' => 'medium_large']), true);
                $ogp['site_name'] = $goal['Goal']['name'];
            }
        }
        return $ogp;
    }

    /**
     * ユーザーの全てのローカル名を返す
     *
     * @param $user_id
     *
     * @return array
     */
    private function _getUserLocalNames($user_id)
    {
        $user_local_names = [];
        $rows = ClassRegistry::init('LocalName')->getAllByUserId($user_id);
        foreach ($rows as $v) {
            // 姓名の並び順を考慮したローカルフルネーム
            $v['LocalName']['local_username'] =
                ClassRegistry::init('User')->buildLocalUserName($v['LocalName']['language'],
                    $v['LocalName']['first_name'],
                    $v['LocalName']['last_name']);
            $user_local_names[$v['LocalName']['language']] = $v['LocalName'];
        }
        return $user_local_names;
    }

    /**
     * Fetches a URI and parses it for Open Graph data, returns
     * false on error.
     *
     * @param $URI    URI to page to parse for Open Graph data
     *
     * @return mixed
     */
    public function fetch($URI)
    {
        $curl = curl_init($URI);

        curl_setopt($curl, CURLOPT_FAILONERROR, true);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_TIMEOUT, 15);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_USERAGENT, env('HTTP_USER_AGENT'));

        $response = curl_exec($curl);
        //文字化け対策
        $charset = mb_detect_encoding($response, "JIS, eucjp-win, sjis-win, UTF8");
        $response = mb_convert_encoding($response, 'HTML-ENTITIES', empty($charset) ? 'auto' : $charset);

        curl_close($curl);

        if (!empty($response)) {
            /** @noinspection PhpParamsInspection */
            return self::_parse($response);
        } else {
            // エラーログに出力するとノイズになるためコメントアウト。
            // fetchとしたURLが存在しないケースは結構ある。
            // $this->log("Failed to fetch OGP info. url=$URI");
            return false;
        }
    }

    /**
     * Parses HTML and extracts Open Graph data, this assumes
     * the document is at least well formed.
     *
     * @param $HTML    HTML to parse
     *
     * @return mixed
     */
    static private function _parse($HTML)
    {
        $old_libxml_error = libxml_use_internal_errors(true);

        $doc = new DOMDocument();
        $doc->loadHTML($HTML);
        libxml_use_internal_errors($old_libxml_error);

        /**
         * @var DOMNodeList $tags
         */
        $tags = $doc->getElementsByTagName('meta');
        if (!$tags || $tags->length === 0) {
            return false;
        }

        $page = new self();

        $nonOgDescription = null;

        foreach ($tags AS $tag) {
            /**
             * @var DOMElement $tag
             */
            if ($tag->hasAttribute('property') &&
                strpos($tag->getAttribute('property'), 'og:') === 0
            ) {
                $key = strtr(substr($tag->getAttribute('property'), 3), '-', '_');
                if (isset($page->_values[$key])) {
                    continue;
                }
                $page->_values[$key] = $tag->getAttribute('content');
            }

            //Added this if loop to retrieve description values from sites like the New York Times who have malformed it.
            if ($tag->hasAttribute('value') && $tag->hasAttribute('property') &&
                strpos($tag->getAttribute('property'), 'og:') === 0
            ) {
                $key = strtr(substr($tag->getAttribute('property'), 3), '-', '_');
                $page->_values[$key] = $tag->getAttribute('value');
            }
            //Based on modifications at https://github.com/bashofmann/opengraph/blob/master/src/OpenGraph/OpenGraph.php
            if ($tag->hasAttribute('name') && strtolower($tag->getAttribute('name')) === 'description') {
                $nonOgDescription = $tag->getAttribute('content');
            }

        }
        //Based on modifications at https://github.com/bashofmann/opengraph/blob/master/src/OpenGraph/OpenGraph.php
        if (!isset($page->_values['title'])) {
            $titles = $doc->getElementsByTagName('title');
            if ($titles->length > 0) {
                $page->_values['title'] = $titles->item(0)->textContent;
            }
        }
        if (!isset($page->_values['description']) && $nonOgDescription) {
            $page->_values['description'] = $nonOgDescription;
        }
        //Fallback to use image_src if ogp::image isn't set.
        if (!isset($page->_values['image'])) {
            $domxpath = new DOMXPath($doc);
            $elements = $domxpath->query("//link[@rel='image_src']");

            if ($elements->length > 0) {
                /**
                 * @var DOMNode $domattr
                 */
                $domattr = $elements->item(0)->attributes->getNamedItem('href');

                if ($domattr) {
                    $page->_values['image'] = $domattr->nodeValue;
                    $page->_values['image_src'] = $domattr->nodeValue;
                }
            }
        }

        if (empty($page->_values)) {
            return false;
        }

        return $page;
    }

    /**
     * Helper method to access attributes directly
     * Example:
     * $graph->title
     *
     * @param $key    Key to fetch from the lookup
     *
     * @return int|string
     */
    public function __get($key)
    {
        if (array_key_exists($key, $this->_values)) {
            /** @noinspection PhpIllegalArrayKeyTypeInspection */
            return $this->_values[$key];
        }

        if ($key === 'schema') {
            foreach (self::$TYPES AS $schema => $types) {
                if (array_search($this->_values['type'], $types)) {
                    return $schema;
                }
            }
        }
        return null;
    }

    /**
     * Return all the keys found on the page
     *
     * @return array
     */
    public function keys()
    {
        return array_keys($this->_values);
    }

    /**
     * Helper method to check an attribute exists
     *
     * @param $key
     *
     * @return bool
     */
    public function __isset($key)
    {
        return array_key_exists($key, $this->_values);
    }

    /**
     * Will return true if the page has location data embedded
     *
     * @return boolean Check if the page has location data
     */
    public function hasLocation()
    {
        if (array_key_exists('latitude', $this->_values) && array_key_exists('longitude', $this->_values)) {
            return true;
        }

        $address_keys = array('street_address', 'locality', 'region', 'postal_code', 'country_name');
        $valid_address = true;
        foreach ($address_keys AS $key) {
            $valid_address = ($valid_address && array_key_exists($key, $this->_values));
        }
        return $valid_address;
    }

    /**
     * Iterator code
     */
    private $_position = 0;

    public function rewind()
    {
        reset($this->_values);
        $this->_position = 0;
    }

    public function current()
    {
        return current($this->_values);
    }

    public function key()
    {
        return key($this->_values);
    }

    public function next()
    {
        next($this->_values);
        ++$this->_position;
    }

    public function valid()
    {
        return $this->_position < sizeof($this->_values);
    }

}

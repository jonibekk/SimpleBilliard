<?php

/**
 * Textヘルパーを拡張

 *
*@author daikihirakata
 * @property SessionHelper $Session
 * @property TextHelper    $Text
 * @property HtmlHelper    $Html
 */
class TextExHelper extends AppHelper
{

    /**
     * An array of md5sums and their contents.
     * Used when inserting links into text.
     *
     * @var array
     */
    protected $_placeholders = array();

    public $helpers = array(
        'Text',
        'Html',
    );

    function autoLink($text)
    {
        return nl2br($this->autoLinkUrlsEx(h($text), ['target' => 'blank', 'escape' => false]));
    }

    /**
     * Adds links (<a href=....) to a given text, by finding text that begins with
     * strings like http:// and ftp://.
     * ### Options
     * - `escape` Control HTML escaping of input. Defaults to true.
     *
     * @param string $text    Text
     * @param array  $options Array of HTML options, and options listed above.
     *
     * @return string The text with links
     * @link http://book.cakephp.org/2.0/en/core-libraries/helpers/text.html#TextHelper::autoLinkUrls
     */
    public function autoLinkUrlsEx($text, $options = array())
    {
        $this->_placeholders = array();
        $options += array('escape' => true);

        $pattern = '#(?<!href="|src="|">)((?:https?|ftp|nntp)://[\p{L}0-9.\-_:]+(?:[/?][^\s<]*)?)#ui';
        $text = preg_replace_callback(
            $pattern,
            array(&$this, '_insertPlaceHolder'),
            $text
        );
        if ($options['escape']) {
            $text = h($text);
        }
        return $this->_linkUrls($text, $options);
    }

    /**
     * Replace placeholders with links.
     *
     * @param string $text        The text to operate on.
     * @param array  $htmlOptions The options for the generated links.
     *
     * @return string The text with links inserted.
     */
    protected function _linkUrls($text, $htmlOptions)
    {
        $replace = array();
        foreach ($this->_placeholders as $hash => $url) {
            $link = $url;
            if (!preg_match('#^[a-z]+\://#', $url)) {
                $url = 'http://' . $url;
            }
            $replace[$hash] = $this->Html->link($link, $url, $htmlOptions);
        }
        return strtr($text, $replace);
    }

    /**
     * Saves the placeholder for a string, for later use. This gets around double
     * escaping content in URL's.
     *
     * @param array $matches An array of regexp matches.
     *
     * @return string Replaced values.
     */
    protected function _insertPlaceHolder($matches)
    {
        $key = md5($matches[0]);
        $this->_placeholders[$key] = $matches[0];
        return $key;
    }
}

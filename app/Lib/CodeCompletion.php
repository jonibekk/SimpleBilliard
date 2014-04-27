<?php
/**
 * Code compleation for CakePHP Helpers in Eclipse PDT
 *
 * @author   nojimage (http://php-tips.com/)
 * @original Mark Story | http://mark-story.com/posts/view/code-completion-for-cakephp-in-eclipse
 */
// stop page loading
exit;

// reference helpers so Eclipse provides code completion
// -- Deprecated Helper
/*
$ajax = new AjaxHelper();
$javascript = new JavascriptHelper();

// -- Core Helpers
$cache = new CacheHelper();
$form = new FormHelper();
$html = new HtmlHelper();
$js = new JsHelper();
$number = new NumberHelper();
$paginator = new PaginatorHelper();
$rss = new RssHelper();
$session = new SessionHelper();
$text = new TextHelper();
$time = new TimeHelper();
$xml = new XmlHelper();
 */

// -- Plugin Helpers
#$custom = new CustomHelper();

// -- View
// If you want code hint can't write each view files. comment out this.
/** @noinspection PhpUnreachableStatementInspection */
/** @noinspection PhpDeprecationInspection */
$this = new ThemeView();

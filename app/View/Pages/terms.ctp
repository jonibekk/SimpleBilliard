<?php
/**
 * PHP 5
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright 2005-2012, Cake Software Foundation, Inc. (http://cakefoundation.org)
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 2005-2012, Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       Cake.View.Pages
 * @since         CakePHP(tm) v 0.10.0.1076
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 * @var CodeCompletionView $this
 * @var                    $user_count
 * @var                    $top_lang
 */
?>
<?php $this->append('meta') ?>
<link rel="alternate" hreflang="ja" href="<?= $this->Html->url('/ja/terms') ?>"/>
<link rel="alternate" hreflang="en" href="<?= $this->Html->url('/en/terms') ?>"/>
<link rel="alternate" hreflang="x-default" href="<?= $this->Html->url('/terms') ?>"/>
<?php $this->end() ?>
<!-- START app/View/Pages/terms.ctp -->
<div id="markdown" class="markdown-wrap" src="../../markdowns/jp_terms.md"></div>
<!-- END app/View/Pages/terms.ctp -->

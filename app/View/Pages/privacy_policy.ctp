<?php /**
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
<link rel="alternate" hreflang="ja" href="<?= $this->Html->url('/ja/privacy_policy') ?>"/>
<link rel="alternate" hreflang="en" href="<?= $this->Html->url('/en/privacy_policy') ?>"/>
<link rel="alternate" hreflang="x-default" href="<?= $this->Html->url('/privacy_policy') ?>"/>
<?php $this->end() ?>
<!-- START app/View/Pages/privacy_policy.ctp -->
<!-- 言語で読み込むファイルを切り替えたい -->
<div id="markdown" class="markdown-wrap" src="../../composition/markdowns/jp_privacy_policy.md"></div>
<!-- END app/View/Pages/privacy_policy.ctp -->

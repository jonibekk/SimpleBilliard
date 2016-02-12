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
<link rel="alternate" hreflang="ja" href="<?= $this->Html->url('/ja/law') ?>"/>
<link rel="alternate" hreflang="en" href="<?= $this->Html->url('/en/law') ?>"/>
<link rel="alternate" hreflang="x-default" href="<?= $this->Html->url('/law') ?>"/>
<?php $this->end() ?>
<!-- START app/View/Pages/law.ctp -->

<!-- ToDo - 言語で読み込むmdファイルを変えたい -->
<div id="markdown" src="../../composition/markdowns/jp_law.md" class="markdown-wrap"></div>
<!-- END app/View/Pages/law.ctp -->

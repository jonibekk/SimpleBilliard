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
<div id="law-mark"></div>

<?= __d('lp', '特定商取引法に基づく表記

販売業者
　株式会社ISAO
販売責任者
　代表取締役 中村圭志
連絡先
　contact@goalous.com
　平日10時～18時
　(土日・祝祭日・年末年始除く)
電話番号
　03-5825-5709
　電話でのお問い合わせは受け付けておりません。
　サポートはメールのみとなります。
　あらかじめご了承いただきますようお願いいたします。
所在地
　東京都台東区浅草橋５丁目20-8　CSタワー７階
販売価格
　2016年9月より有料サービス開始予定
お支払い方法（予定）
　クレジットカード決済
　口座振り込み
引渡し時期
　即時
キャンセル・返品について
　商品の性質上、購入後のキャンセル・返品は原則お受けできません。

') ?>
<!-- END app/View/Pages/law.ctp -->

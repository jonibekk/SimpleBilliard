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
<link rel="alternate" hreflang="ja" href="<?= $this->Html->url('/ja/pricing') ?>"/>
<link rel="alternate" hreflang="en" href="<?= $this->Html->url('/en/pricing') ?>"/>
<link rel="alternate" hreflang="x-default" href="<?= $this->Html->url('/pricing') ?>"/>
<?php $this->end() ?>
<!-- START app/View/Pages/pricing.ctp -->
<!-- ******PRICE PLAN****** -->
<section id="price-plan" class="price-plan section">
    <div class="container text-center">
        <h2 class="title"><?= __d('lp', '今だけ、有料プランも完全無料') ?></h2>
        <p class="intro"><?= __d('lp', 'リリース記念で2016年8月までPlusをご利用いただけます。フィードバックをお待ちしております。') ?></p>
        <div class="price-cols row">
            <div class="item col-md-4 col-md-offset-2 col-sm-4 col-sm-offset-2 col-xs-12">
                <h3 class="heading"><?= __d('lp', 'Basic') ?></h3>
                <div class="content">
                    <div class="price-figure">
                      <p><?= __d('lp', '1ユーザーあたり') ?></p>
                      <span class="currency"><?= __d('lp', '¥') ?></span>
                      <span class="number"><?= __d('lp', '0') ?></span>
                      <span class="unit"><?= __d('lp', '/月') ?></span>
                    </div>
                    <ul class="list-unstyled feature-list">
                        <li><?= __d('lp', '1チーム10アカウントまで') ?></li>
                        <li><?= __d('lp', '3MB/ファイルのアップロード') ?></li>
                        <li><?= __d('lp', 'ストレージ無制限のファイル共有') ?></li>
                        <li><?= __d('lp', 'チャットメッセージ') ?></li>
                        <li>　</li>
                        <li>　</li>
                        <li>　</li>
                    </ul>
                    <a class="btn btn-cta btn-cta-primary" href="/users/register">
                        <?= __d('lp', 'Plusを試す') ?>
                        <br />
                        <span class="extra"><?= __d('lp', '無料相談受付中') ?></span>
                    </a>
                    <!--a class="btn btn-cta btn-cta-primary" href="#">今すぐ始める<br /><span class="extra">無料相談受付中</span></a-->
                </div><!--//content-->
            </div><!--//item-->

            <div class="item col-md-4 col-sm-4 col-xs-12">
                <h3 class="heading"><?= __d('lp', 'Plus') ?><span class="label label-custom"><?= __d('lp', 'キャンペーン') ?></span></h3>
                <div class="content">
                    <div class="price-figure">
                      <p><?= __d('lp', '1ユーザーあたり') ?></p>
                      <span class="currency"><?= __d('lp', '¥') ?></span><span class="number"><?= __d('lp', '1,980') ?></span><span class="unit"><?= __d('lp', '/月') ?></span>
                    </div>
                    <ul class="list-unstyled feature-list">
                        <li><?= __d('lp', '1チームのアカウント無制限') ?></li>
                        <li><?= __d('lp', '20MB/ファイルのアップロード') ?></li>
                        <li><?= __d('lp', 'ストレージ無制限のファイル共有') ?></li>
                        <li><?= __d('lp', 'チャットメッセージ') ?></li>
                        <li><?= __d('lp', 'インサイト分析') ?></li>
                        <li><?= __d('lp', 'チーム管理機能') ?></li>
                        <li><?= __d('lp', 'オンラインでのユーザーサポート') ?></li>
                    </ul>
                    <a class="btn btn-cta btn-cta-primary" href="/users/register">
                        <?= __d('lp', '今すぐ始める') ?>
                        <br />
                        <span class="extra">
                            <?= __d('lp', '無料相談受付中') ?>
                        </span>
                    </a>
                </div><!--//content-->
            </div><!--//item-->
        </div><!--//row-->
    </div><!--//container-->
</section><!--//price-plan-->

<!-- ******FAQ****** -->
<section id="faq" class="faq section has-bg-color">
    <div class="container">
        <h2 class="title text-center"><?= __d('lp', 'Frequently Asked Questions') ?></h2>
        <div class="row">
            <div class="col-md-6 col-sm-6 col-xs-12">
                <div class="panel">
                    <div class="panel-heading">
                        <h4 class="panel-title">
                            <a data-parent="#accordion" data-toggle="collapse" class="panel-toggle" href="#faq1"><i class="fa fa-plus-square"></i>
                                <?= __d('lp', 'Can I viverra sit amet quam eget lacinia?') ?>
                            </a>
                        </h4>
                    </div>

                    <div class="panel-collapse collapse" id="faq1">
                        <div class="panel-body">
                            <?= __d('lp', "
                                Anim pariatur cliche reprehenderit, enim eiusmod high life
                                accusamus terry richardson ad squid. 3 wolf moon officia
                                aute, non cupidatat skateboard dolor brunch. Food truck
                                quinoa nesciunt laborum eiusmod. Brunch 3 wolf moon tempor,
                                sunt aliqua put a bird on it squid single-origin coffee
                                nulla assumenda shoreditch et. Nihil anim keffiyeh
                                helvetica, craft beer labore wes anderson cred nesciunt
                                sapiente ea proident. Ad vegan excepteur butcher vice lomo.
                                Leggings occaecat craft beer farm-to-table, raw denim
                                aesthetic synth nesciunt you probably haven't heard of them
                                accusamus labore sustainable VHS.
                            ") ?>
                        </div>
                    </div>
                </div><!--//panel-->

                <div class="panel">
                    <div class="panel-heading">
                        <h4 class="panel-title"><a data-parent="#accordion"
                        data-toggle="collapse" class="panel-toggle" href="#faq2"><i class="fa fa-plus-square"></i><?= __d('lp', 'What is the ipsum dolor sit amet quam tortor?') ?></a></h4>
                    </div>

                    <div class="panel-collapse collapse" id="faq2">
                        <div class="panel-body">
                            <?= __d('lp', "
                                Anim pariatur cliche reprehenderit, enim eiusmod high life
                                accusamus terry richardson ad squid. 3 wolf moon officia
                                aute, non cupidatat skateboard dolor brunch. Food truck
                                quinoa nesciunt laborum eiusmod. Brunch 3 wolf moon tempor,
                                sunt aliqua put a bird on it squid single-origin coffee
                                nulla assumenda shoreditch et. Nihil anim keffiyeh
                                helvetica, craft beer labore wes anderson cred nesciunt
                                sapiente ea proident. Ad vegan excepteur butcher vice lomo.
                                Leggings occaecat craft beer farm-to-table, raw denim
                                aesthetic synth nesciunt you probably haven't heard of them
                                accusamus labore sustainable VHS.
                            ") ?>
                        </div>
                    </div>
                </div><!--//panel-->

                <div class="panel">
                    <div class="panel-heading">
                        <h4 class="panel-title"><a data-parent="#accordion"
                        data-toggle="collapse" class="panel-toggle" href="#faq3"><i class="fa fa-plus-square"></i><?= __d('lp', '') ?>How does the morbi quam tortor work?</a></h4>
                    </div>

                    <div class="panel-collapse collapse" id="faq3">
                        <div class="panel-body">
                            <?= __d('lp', '') ?>
                            Anim pariatur cliche reprehenderit, enim eiusmod high life
                            accusamus terry richardson ad squid. 3 wolf moon officia
                            aute, non cupidatat skateboard dolor brunch. Food truck
                            quinoa nesciunt laborum eiusmod. Brunch 3 wolf moon tempor,
                            sunt aliqua put a bird on it squid single-origin coffee
                            nulla assumenda shoreditch et. Nihil anim keffiyeh
                            helvetica, craft beer labore wes anderson cred nesciunt
                            sapiente ea proident. Ad vegan excepteur butcher vice lomo.
                            Leggings occaecat craft beer farm-to-table, raw denim
                            aesthetic synth nesciunt you probably haven't heard of them
                            accusamus labore sustainable VHS.
                        </div>
                    </div>
                </div><!--//panel-->

                <div class="panel">
                    <div class="panel-heading">
                        <h4 class="panel-title"><a data-parent="#accordion"
                        data-toggle="collapse" class="panel-toggle" href="#faq4"><i class="fa fa-plus-square"></i>Can I ipsum dolor sit amet nascetur ridiculus?</a></h4>
                    </div>

                    <div class="panel-collapse collapse" id="faq4">
                        <div class="panel-body">
                            <?= __d('lp', '') ?>
                            Anim pariatur cliche reprehenderit, enim eiusmod high life
                            accusamus terry richardson ad squid. 3 wolf moon officia
                            aute, non cupidatat skateboard dolor brunch. Food truck
                            quinoa nesciunt laborum eiusmod. Brunch 3 wolf moon tempor,
                            sunt aliqua put a bird on it squid single-origin coffee
                            nulla assumenda shoreditch et. Nihil anim keffiyeh
                            helvetica, craft beer labore wes anderson cred nesciunt
                            sapiente ea proident. Ad vegan excepteur butcher vice lomo.
                            Leggings occaecat craft beer farm-to-table, raw denim
                            aesthetic synth nesciunt you probably haven't heard of them
                            accusamus labore sustainable VHS.
                        </div>
                    </div>
                </div><!--//panel-->
            </div>
            <div class="col-md-6 col-sm-6 col-xs-12">
                <div class="panel">
                    <div class="panel-heading">
                        <h4 class="panel-title"><a data-parent="#accordion"
                        data-toggle="collapse" class="panel-toggle" href="#faq5"><i class="fa fa-plus-square"></i><?= __d('lp', '') ?>Is it possible to tellus eget auctor condimentum?</a></h4>
                    </div>

                    <div class="panel-collapse collapse" id="faq5">
                        <div class="panel-body">
                            <?= __d('lp', '') ?>
                            Anim pariatur cliche reprehenderit, enim eiusmod high life
                            accusamus terry richardson ad squid. 3 wolf moon officia
                            aute, non cupidatat skateboard dolor brunch. Food truck
                            quinoa nesciunt laborum eiusmod. Brunch 3 wolf moon tempor,
                            sunt aliqua put a bird on it squid single-origin coffee
                            nulla assumenda shoreditch et. Nihil anim keffiyeh
                            helvetica, craft beer labore wes anderson cred nesciunt
                            sapiente ea proident. Ad vegan excepteur butcher vice lomo.
                            Leggings occaecat craft beer farm-to-table, raw denim
                            aesthetic synth nesciunt you probably haven't heard of them
                            accusamus labore sustainable VHS.
                        </div>
                    </div>
                </div><!--//panel-->

                <div class="panel">
                    <div class="panel-heading">
                        <h4 class="panel-title"><a data-parent="#accordion"
                        data-toggle="collapse" class="panel-toggle" href="#faq6"><i class="fa fa-plus-square"></i><?= __d('lp', '') ?>Would it elementum turpis semper imperdiet?</a></h4>
                    </div>

                    <div class="panel-collapse collapse" id="faq6">
                        <div class="panel-body">
                            <?= __d('lp', '') ?>
                            Anim pariatur cliche reprehenderit, enim eiusmod high life
                            accusamus terry richardson ad squid. 3 wolf moon officia
                            aute, non cupidatat skateboard dolor brunch. Food truck
                            quinoa nesciunt laborum eiusmod. Brunch 3 wolf moon tempor,
                            sunt aliqua put a bird on it squid single-origin coffee
                            nulla assumenda shoreditch et. Nihil anim keffiyeh
                            helvetica, craft beer labore wes anderson cred nesciunt
                            sapiente ea proident. Ad vegan excepteur butcher vice lomo.
                            Leggings occaecat craft beer farm-to-table, raw denim
                            aesthetic synth nesciunt you probably haven't heard of them
                            accusamus labore sustainable VHS.
                        </div>
                    </div>
                </div><!--//panel-->

                <div class="panel">
                    <div class="panel-heading">
                        <h4 class="panel-title"><a data-parent="#accordion"
                        data-toggle="collapse" class="panel-toggle" href="#faq7"><i class="fa fa-plus-square"></i><?= __d('lp', '') ?>How can I imperdiet lorem sem non nisl?</a></h4>
                    </div>

                    <div class="panel-collapse collapse" id="faq7">
                        <div class="panel-body">
                            <?= __d('lp', '') ?>
                            Anim pariatur cliche reprehenderit, enim eiusmod high life
                            accusamus terry richardson ad squid. 3 wolf moon officia
                            aute, non cupidatat skateboard dolor brunch. Food truck
                            quinoa nesciunt laborum eiusmod. Brunch 3 wolf moon tempor,
                            sunt aliqua put a bird on it squid single-origin coffee
                            nulla assumenda shoreditch et. Nihil anim keffiyeh
                            helvetica, craft beer labore wes anderson cred nesciunt
                            sapiente ea proident. Ad vegan excepteur butcher vice lomo.
                            Leggings occaecat craft beer farm-to-table, raw denim
                            aesthetic synth nesciunt you probably haven't heard of them
                            accusamus labore sustainable VHS.
                        </div>
                    </div>
                </div><!--//panel-->

                <div class="panel">
                    <div class="panel-heading">
                        <h4 class="panel-title"><a data-parent="#accordion"
                        data-toggle="collapse" class="panel-toggle" href="#faq8"><i class="fa fa-plus-square"></i><?= __d('lp', '') ?>Can I imperdiet massa ut?</a></h4>
                    </div>

                    <div class="panel-collapse collapse" id="faq8">
                        <div class="panel-body">
                            <?= __d('lp', '') ?>
                            Anim pariatur cliche reprehenderit, enim eiusmod high life
                            accusamus terry richardson ad squid. 3 wolf moon officia
                            aute, non cupidatat skateboard dolor brunch. Food truck
                            quinoa nesciunt laborum eiusmod. Brunch 3 wolf moon tempor,
                            sunt aliqua put a bird on it squid single-origin coffee
                            nulla assumenda shoreditch et. Nihil anim keffiyeh
                            helvetica, craft beer labore wes anderson cred nesciunt
                            sapiente ea proident. Ad vegan excepteur butcher vice lomo.
                            Leggings occaecat craft beer farm-to-table, raw denim
                            aesthetic synth nesciunt you probably haven't heard of them
                            accusamus labore sustainable VHS.
                        </div>
                    </div>
                </div><!--//panel-->
            </div>
        </div><!--//row-->
        <div class="more text-center">
            <h4 class="title"><?= __d('lp', '') ?>More questions?</h4>
            <a class="btn btn-cta btn-cta-secondary" href="contact.html"><?= __d('lp', '') ?>Get in touch</a>
        </div>
    </div><!--//container-->
</section><!--//faq-->

<?= $this->element('Homepage/signup') ?>
<!-- END app/View/Pages/pricing.ctp -->

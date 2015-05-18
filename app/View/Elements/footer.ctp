<?php
/**
 * Created by PhpStorm.
 * User: bigplants
 * Date: 5/28/14
 * Time: 5:07 PM
 *
 * @var $title_for_layout string
 * @var $this             View
 */
?>
<!-- START app/View/Elements/footer.ctp -->
<footer class="col-xxs-12">
    <div class="row">
        <div class="col-lg-12">

            <ul class="list-unstyled">
                <li id="gotop-text">トップへ戻る</li>
                <li id="gotop" class="pull-right"><!--suppress HtmlUnknownAnchorTarget -->
                    <a href="#top"><i class="fa fa-arrow-up"></i></a></li>
                <li><?=
                    $this->Html->link(__d('home', 'Features'),
                                      [
                                          'controller' => 'pages',
                                          'action'     => 'display',
                                          'pagename'   => 'features',
                                      ])
                    ?></li>
                <li><?=
                    $this->Html->link(__d('home', 'Blog'), 'http://blog.goalous.com/',
                                      ['target' => '_blank']) ?></li>
                <li><?=
                    $this->Html->link(__d('gl', 'Privacy Policy'),
                                      [
                                          'controller' => 'pages',
                                          'action'     => 'display',
                                          'pagename'   => 'pp',
                                      ])
                    ?></li>
                <li><?=
                    $this->Html->link(__d('gl', 'Terms of Service'),
                                      [
                                          'controller' => 'pages',
                                          'action'     => 'display',
                                          'pagename'   => 'tos',
                                      ])
                    ?></li>
            </ul>
            <p><?php $link = $this->Html->link(__d('gl', "Isao Corporation"), "http://www.isao.co.jp/",
                                               ['target' => "_blank"]);
                echo __d('gl', "Made By %s", $link) ?></p>
        </div>
    </div>
</footer>
<div id="layer-black"></div>
<!-- END app/View/Elements/footer.ctp -->

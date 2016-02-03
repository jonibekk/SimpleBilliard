<?php
/**
 * Created by PhpStorm.
 * User: bigplants
 * Date: 5/28/14
 * Time: 5:07 PM
 *
 * @var CodeCompletionView $this
 * @var                    $title_for_layout string
 * @var                    $this             View
 */
?>
<!-- START app/View/Elements/footer.ctp -->
<footer class="col-xxs-12 <?= $is_mb_app ? "hide" : null ?>">
    <div class="row">
        <div class="col-lg-12">

            <ul class="list-unstyled">
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
            <p>© 2016 ISAO</p>
        </div>
    </div>
</footer>
<div id="layer-black"></div>
<!-- END app/View/Elements/footer.ctp -->

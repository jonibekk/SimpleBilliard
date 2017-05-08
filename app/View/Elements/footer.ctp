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
<?= $this->App->viewStartComment()?>
<footer class="col-xxs-12 <?= $is_mb_app ? "hide" : null ?>">
    <div class="row">
        <div class="col-lg-12">

            <ul class="list-unstyled">
                <li><?=
                    $this->Html->link(__('Blog'), 'http://blog.goalous.com/',
                        ['target' => '_blank']) ?></li>
                <li><?=
                    $this->Html->link(__('Privacy Policy'),
                        [
                            'controller' => 'pages',
                            'action'     => 'display',
                            'pagename'   => 'privacy_policy',
                        ],
                        [
                            'target'  => "blank",
                            'onclick' => "window.open(this.href,'_system');return false;",
                        ]
                    )
                    ?></li>
                <li><?=
                    $this->Html->link(__('Terms of Service'),
                        [
                            'controller' => 'pages',
                            'action'     => 'display',
                            'pagename'   => 'terms',
                        ],
                        [
                            'target'  => "blank",
                            'onclick' => "window.open(this.href,'_system');return false;",
                        ]
                    )
                    ?></li>
            </ul>
            <p>&copy; <?php echo date("Y"); ?> ISAO</p>
        </div>
    </div>
</footer>
<div id="layer-black"></div>
<?= $this->App->viewEndComment()?>

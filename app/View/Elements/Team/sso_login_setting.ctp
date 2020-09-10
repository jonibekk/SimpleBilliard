<?php

?>
<?= $this->App->viewStartComment() ?>
<section class="panel panel-default">
    <header>
        <h2><?= __("SAML Authentication") ?></h2>
    </header>
    <div class="panel-body form-horizontal">
        <p class="form-control-static">
            <?= __("Get set up with SAML2.0 solution.") ?></p>
        <?php
        if (!empty($hasSsoSetting)): ?>
            <div class="form-group">
                <?=
                $this->Html->link(
                    __("Update SSO Setting"),
                    "/settings/sso",
                    ['class' => 'btn btn-default', 'div' => false]
                ) ?>
            </div>
        <?php
        endif; ?>
    </div>
    <footer>
        <?php
        if (!empty($hasSsoSetting)): ?>
            <?=
            $this->Form->postLink(
                __("Stop Using the SSO"),
                [
                    'controller' => 'teams',
                    'action'     => 'delete_sso_setting'
                ],
                ['class' => 'btn btn-primary', 'div' => false],
                __("Would you like to pause previous term evaluations?")
            ) ?>

        <?php
        else: ?>
            <?=
            $this->Html->link(
                __("Configure SSO"),
                "/settings/sso",
                ['class' => 'btn btn-primary', 'div' => false]
            ) ?>
        <?php
        endif; ?>
    </footer>
</section>
<?= $this->App->viewEndComment() ?>

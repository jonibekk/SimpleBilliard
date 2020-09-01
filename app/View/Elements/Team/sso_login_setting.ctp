<?php
?>
<?= $this->App->viewStartComment() ?>
<section class="panel panel-default">
    <header>
        <h2><?= __("SSO Login Setting") ?></h2>
    </header>
    <div class="panel-body">
        <?=
        $this->Html->link(
            __("Set SAML2.0 Configuration"),
            "/settings/sso_login",
            ['class' => 'btn btn-primary', 'div' => false]
        ) ?>
    </div>
</section>
<?= $this->App->viewEndComment() ?>

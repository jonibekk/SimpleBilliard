<?php

?>
<?= $this->App->viewStartComment() ?>
<section class="panel panel-default">
    <header>
        <h2><?= __("SAML Authentication") ?></h2>
    </header>
    <div class="panel-body form-horizontal">
        <?= __("Get set up with SAML2.0 solution.") ?>
    </div>
    <footer>
        <?=
        $this->Html->link(
            __("Configure SSO"),
            "/settings/sso_login",
            ['class' => 'btn btn-primary', 'div' => false]
        ) ?></footer>
</section>
<?= $this->App->viewEndComment() ?>

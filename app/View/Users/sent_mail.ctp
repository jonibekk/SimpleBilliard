<?php
/**
 * @var CodeCompletionView $this
 * @var                    $email
 */
?>
<!-- START app/View/Users/sent_mail.ctp -->
<div class="jumbotron jumbotron-icon text-center">
    <i class="fa fa-check-square fa-5 color-blue"></i>

    <h1><?= __("Congratulations!") ?></h1>

    <p><?= __("You successfully made your account.") ?></p>

    <p><?= __("Sent an email to %s. Click on the URL in the email to authenticate the account.",
            "<b>" . $email . "</b>") ?></p>
</div>
<!-- END app/View/Users/sent_mail.ctp -->

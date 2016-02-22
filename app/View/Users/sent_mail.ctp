<?php
/**
 * @var CodeCompletionView $this
 * @var                    $email
 */
?>
<!-- START app/View/Users/sent_mail.ctp -->
<div class="jumbotron jumbotron-icon text-center">
    <i class="fa fa-check-square fa-5 color-blue"></i>

    <h1><?= __d('app', "おめでとうございます！") ?></h1>

    <p><?= __d('app', "あなたはフリートライアルとしてGoalousに登録されました。") ?></p>

    <p><?= __d('app', "%sへメールを送信しました。アカウントを認証する為に、そのメールに記載されたURLをクリックして下さい。", "<b>" . $email . "</b>") ?></p>
</div>
<!-- END app/View/Users/sent_mail.ctp -->

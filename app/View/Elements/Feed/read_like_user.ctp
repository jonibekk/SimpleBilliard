<?php
/**
 * Created by PhpStorm.
 * User: daikihirakata
 * Date: 2014/07/19
 * Time: 23:28
 *
 * @var CodeCompletionView $this
 * @var                    $user
 * @var                    $created
 */
?>
<!-- START app/View/Elements/Feed/read_like_user.ctp -->
<div class="col col-xxs-12 mpTB0">
    <?=
    $this->Upload->uploadImage($user, 'User.photo', ['style' => 'small'],
                               ['class' => 'gl-comment-img'])
    ?>
    <div class="gl-comment-body mpTB0">
        <div class="font-size_12 font-weight_bold modalFeedTextPadding">
            <?= h($user['display_username']) ?></div>

        <div class="font-size_12 color9197a3 modalFeedTextPaddingSmall">
            <?= $this->TimeEx->elapsedTime(h($created)) ?>
        </div>
    </div>
</div>
<!-- END app/View/Elements/Feed/read_like_user.ctp -->

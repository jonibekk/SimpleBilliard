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
<div class="col col-xxs-12">
    <?=
    $this->Upload->uploadImage($user, 'User.photo', ['style' => 'small'],
                               ['class' => 'gl-comment-img'])
    ?>
    <div class="gl-comment-body"><span>
                    <?= h($user['display_username']) ?></span>

        <div>
            <?= $this->TimeEx->elapsedTime(h($created)) ?>
        </div>
    </div>
</div>

<?php
/**
 * @var $followers
 */
?>
<?php if ($followers): ?>
    <!-- START app/View/Elements/Goal/follower_list.ctp -->
    <?php foreach ($followers as $follower): ?>
        <div class="col col-xxs-12 mpTB0">
            <?=
            $this->Upload->uploadImage($follower['User'], 'User.photo', ['style' => 'small'],
                                       ['class' => 'comment-img'])
            ?>
            <div class="comment-body modal-comment">
                <div class="font_12px font_bold modalFeedTextPadding">
                    <?= h($follower['User']['display_username']) ?></div>

                <?php if ($follower['Follower']['created']): ?>
                    <div class="font_12px font_lightgray modalFeedTextPaddingSmall">
                        <?= $this->TimeEx->elapsedTime(h($follower['Follower']['created']), 'rough') ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    <?php endforeach ?>
    <!-- START app/View/Elements/Goal/follower_list.ctp -->
<? endif ?>

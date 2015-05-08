<?php
/**
 * Created by PhpStorm.
 * User: saeki
 * Date: 15/04/27
 * Time: 16:57
 *
 * @var $user
 * @var $notification
 * @var $is_unread
 */
?>

<!-- START app/View/Elements/Notification/notify_item.ctp -->
<? $unread_class = $notification['unread_flg'] ? 'unread_notify' : 'read_notify'; ?>

<li class="divider notify-divider"></li>
<li class="notify-card-list <?= $unread_class ?>" data-score="<?= $notification['score'] ?>">
    <a href="<?= $notification['url'] ?>" class="col col-xxs-12 notify-card" id="notifyCard">
        <?=
        $this->Html->image(
            $this->Upload->uploadUrl(
                $user,
                'User.photo',
                ['style' => 'medium_large']
            ),
            array(
                'class' => array('pull-left notify-icon')
            )
        );
        ?>
        <div class="comment-body col-xxs-9 notify-contents">
            <div class="col col-xxs-12 comment-text comment-user">
                <div class="mb_2px lh_12px">
                    <span class="font_bold font_verydark">
                        <?= h($notification['title']) ?>
                    </span>
                </div>
            </div>
            <div
                class="col col-xxs-12 showmore-comment comment-text feed-contents comment-contents font_verydark box-align notify-text notify-line-number"
                id="CommentTextBody_67">
                <? if (NotifySetting::$TYPE[$notification['type']]['icon_class']): ?><i
                    class="fa <?= NotifySetting::$TYPE[$notification['type']]['icon_class'] ?> disp_i"></i><? endif; ?>
                「<?= mb_strimwidth(h(json_decode($notification['body'])[0]), 0, 20, '..') ?>」
                <p><?= $this->TimeEx->elapsedTime(h($notification['created'])) ?></p>
            </div>
        </div>
    </a>
</li>

<!-- END app/View/Elements/Notification/notify_item.ctp -->

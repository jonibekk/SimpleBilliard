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
 * @var $team
 */
?>

<!-- START app/View/Elements/Notification/notify_item.ctp -->
<?php $unread_class = $notification['unread_flg'] ? 'notify-card-unread' : 'notify-card-read'; ?>

<li class="notify-card-list <?= $unread_class ?>" data-score="<?= $notification['score'] ?>">
    <a href="<?= $notification['url'] ?>" class="col col-xxs-12 notify-card" id="notifyCard">
        <?php if (!empty($user)): ?>
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
        <?php else: ?>
            <?=
            $this->Html->image(
                $this->Upload->uploadUrl(
                    $team,
                    'Team.photo',
                    ['style' => 'medium_large']
                ),
                array(
                    'class' => array('pull-left notify-icon')
                )
            );
            ?>
        <?php endif; ?>
        <div class="col-xxs-9 notify-contents">
            <div class="col col-xxs-12 notify-card-head">
              <span class="font_bold font_verydark">
                  <?= h($notification['title']) ?>
              </span>
            </div>
            <div
                class="col col-xxs-12 showmore-comment feed-contents comment-contents notify-text notify-line-number notify-card-text"
                id="CommentTextBody_67">
                <?php if (NotifySetting::$TYPE[$notification['type']]['icon_class']): ?><i
                    class="fa <?= NotifySetting::$TYPE[$notification['type']]['icon_class'] ?> disp_i font_bold"></i><?php endif; ?>
                <?= mb_strimwidth(h(json_decode($notification['body'])[0]), 0, 36, '..') ?>
            </div>
            <p class="notify-card-aside"><?= $this->TimeEx->elapsedTime(h($notification['created'])) ?></p>
        </div>
    </a>
</li>
<li class="divider notify-divider"></li>

<!-- END app/View/Elements/Notification/notify_item.ctp -->

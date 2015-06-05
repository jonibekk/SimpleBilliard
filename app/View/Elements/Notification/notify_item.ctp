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
 * @var $location_type
 */
switch ($location_type) {
    case "page":
        $list_type_class = "notify-page-card";//TODO クラス名を変更する
        $card_type_class = "notify-page-card-letters";
        break;
    case "dropdown":
        $list_type_class = "notify-dropdown-card";//TODO クラス名を変更する
        $card_type_class = "notify-dropdown-card-letters";
        break;
    default:
        $list_type_class = null;
        $card_type_class = null;
}
?>
<!-- START app/View/Elements/Notification/notify_item.ctp -->
<?php $status_read = $notification['unread_flg'] ? 'notify-card-unread' : 'notify-card-read'; ?>
<li class="notify-card-list <?= $status_read ?> <?= $list_type_class ?>" data-score="<?= $notification['score'] ?>">
    <a href="<?= $notification['url'] ?>" class="col col-xxs-12 notify-card-link" id="notifyCard">
        <!-- <div class="notify-card-pic-box"> -->
          <?php if (!empty($user)): ?>
              <?=
              $this->Html->image(
                  $this->Upload->uploadUrl(
                      $user,
                      'User.photo',
                      ['style' => 'medium_large']
                  ),
                  array(
                      'class' => array('pull-left notify-card-pic')
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
                      'class' => array('pull-left notify-card-pic')
                  )
              );
              ?>
          <?php endif; ?>
        <!-- </div> -->
        <div class="notify-contents  <?= $card_type_class ?>">
            <div class="col col-xxs-12 notify-card-head">
              <span class="font-heading">
                  <?= h($notification['title']) ?>
              </span>
            </div>
            <div class="col col-xxs-12 notify-text notify-line-number notify-card-text" id="CommentTextBody_67">
                <?php if (NotifySetting::$TYPE[$notification['type']]['icon_class']): ?>
                  <i class="fa <?= NotifySetting::$TYPE[$notification['type']]['icon_class'] ?> font_bold"></i>
                <?php endif; ?>
                <?= h(json_decode($notification['body'])[0]);  ?>
            </div>
            <p class="notify-card-aside"><?= $this->TimeEx->elapsedTime(h($notification['created'])) ?></p>
        </div>
    </a>
</li>
<li class="divider notify-divider"></li>

<!-- END app/View/Elements/Notification/notify_item.ctp -->

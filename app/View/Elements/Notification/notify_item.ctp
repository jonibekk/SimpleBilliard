<?php
/**
 * Created by PhpStorm.
 * User: saeki
 * Date: 15/04/27
 * Time: 16:57
 *
 * @var CodeCompletionView $this
 * @var                    $user
 * @var                    $notification
 * @var                    $is_unread
 * @var                    $team
 * @var                    $location_type
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
<?= $this->App->viewStartComment() ?>
<?php $status_read = $notification['unread_flg'] ? 'notify-card-unread' : 'notify-card-read'; ?>
<li class="notify-card-list <?= $status_read ?> <?= h($list_type_class) ?>" data-score="<?= $notification['score'] ?>">
    <a href="<?= h($notification['url']) ?>"
       class="notify-card-link"
       id="notifyCard"
       get-url="<?= h($notification['url']) ?>">

        <div class="left">
            <?php if (!empty($user)): ?>
                <?=
                $this->Html->image(
                    $this->Upload->uploadUrl(
                        $user,
                        'User.photo',
                        ['style' => 'medium_large']
                    ),
                    array(
                        'class' => array('')
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
                        'class' => array('')
                    )
                );
                ?>
            <?php endif; ?>
        </div>
        <div class="right">
            <p class="title">
                <?=
                // HTMLが入るのでエスケープしない
                // NotifySetting::getTitle() 内で必要な処理を行っている
                $notification['title']
                ?>
            </p>
            <p class="body">
                <i class="material-icons"><?= Hash::get(NotifySetting::$TYPE, $notification['type'].".icon_class")?></i>
                <?= h(json_decode($notification['body'])[0]); ?>
            </p>
            <span class="time"><?= $this->TimeEx->elapsedTime(h($notification['created'])) ?></span>
        </div>
    </a>
</li>

<?= $this->App->viewEndComment() ?>

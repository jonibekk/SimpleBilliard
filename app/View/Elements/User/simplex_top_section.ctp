<?php
/**
 * Created by PhpStorm.
 * User: bigplants
 * Date: 7/9/15
 * Time: 3:50 PM
 *
 * @var CodeCompletionView $this
 * @var                    $user
 * @var                    $post_count
 * @var                    $action_count
 * @var                    $like_count
 */
?>
<?= $this->Html->script('view/mypage') ?>
<div id="MyPage" class="panel-body profile-user-upper-panel">
    <div class="profile-user-header">
        <div class="dropdown">
            <?php $noCoverClass = empty($user['User']['cover_photo_file_name']) ? "mod-no-image" : ""; ?>
            <a href="#" class="profile-user-cover <?= $noCoverClass ?>"
               data-toggle="dropdown" id="coverMenu">
                <?php if (!empty($user['User']['cover_photo_file_name'])): ?>
                    <?php $coverImageUrl = $this->Upload->uploadUrl($user['User'], 'User.cover_photo',
                        ['style' => 'x_large']); ?>
                    <?= $this->Html->image($coverImageUrl,
                        ['class' => 'profile-user-cover-image'])
                    ?>
                <?php else: ?>
                    <div class="profile-user-cover-default"></div>
                <?php endif; ?>
            </a>

            <ul class="profile-user-dropdown-menu mod-cover dropdown-menu "
                aria-labelledby="coverMenu">
                <li class="profile-user-dropdown-menu-item">
                    <a class="" href="/users/settings#profile">
                        <?= __('Upload Cover Image') ?>
                    </a>
                </li>
                <?php if (!empty($user['User']['cover_photo_file_name'])): ?>
                    <li class="profile-user-dropdown-menu-item">
                        <a class="" href="<?= $coverImageUrl ?>"
                           rel='lightbox'>
                            <?= __('View Cover Image') ?>
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>

        <div class="profile-user-cover-inner-btn">
            <?php if ($this->Session->read('Auth.User.id') == $user['User']['id']): ?>
                <a href="/users/settings#profile" class="btn-pink-radius">
                    <i class="fa fa-pencil mr_5px"></i><span><?= __('Edit Profile') ?></span>
                </a>
            <?php else: ?>
                <a href="/posts/message_list?userId=<?= $user['User']['id'] ?>" class="btn-pink-radius">
                    <i class="fa fa-paper-plane-o mr_5px"></i><span><?= __('Message') ?></span>
                </a>
            <?php endif; ?>
        </div>
        <div class="profile-user-avatar-wrap dropdown">
            <?php $profileImageUrl = $this->Upload->uploadUrl($user['User'], 'User.photo', ['style' => 'x_large']); ?>
            <?php if ($this->Session->read('Auth.User.id') == $user['User']['id']): ?>
                <a href="#" data-toggle="dropdown" id="profileMenu">
                    <?=
                    $this->Html->image(
                        $profileImageUrl,
                        ['class' => 'lazy profile-user-avatar',]
                    )
                    ?>
                </a>
            <?php else: ?>
                <?=
                $this->Html->image(
                    $profileImageUrl,
                    ['class' => 'lazy profile-user-avatar',]
                )
                ?>
            <?php endif ?>
            <ul class="profile-user-dropdown-menu mod-profile dropdown-menu "
                aria-labelledby="profileMenu">
                <li class="profile-user-dropdown-menu-item">
                    <a class="" href="/users/settings#profile">
                        <?= __('Upload Profile Image') ?>
                    </a>
                </li>
                <?php if (!empty($user['User']['photo_file_name'])): ?>
                    <li class="profile-user-dropdown-menu-item">
                        <a class="" href="<?= $profileImageUrl ?>"
                           rel='lightbox'>
                            <?= __('View Profile Image') ?>
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>


    </div>
    <div class="profile-user-info-wrap">
        <p class="profile-user-name">
            <?= h($user['User']['display_username']) ?>
        </p>
        <div class="profile-user-numbers-wrap">
            <div class="profile-user-numbers">
                <div class="profile-user-numbers-action-counts">
                    <?= h($action_count) ?>
                </div>
                <span class="profile-user-numbers-category-action">
                    <?= __('Action') ?>
                </span>
            </div>
            <div class="profile-user-numbers">
                <div class="profile-user-numbers-post-counts">
                    <?= h($post_count) ?>
                </div>
                <span class="profile-user-numbers-category-post">
                    <?= __('Posts') ?>
                </span>
            </div>
            <div class="profile-user-numbers">
                <div class="profile-user-numbers-like-counts">
                    <?= h($this->NumberEx->formatHumanReadable($like_count, ['convert_start' => 10000])) ?>
                </div>
                <span class="profile-user-numbers-category-like">
                    <?= __('Like!') ?>
                </span>
            </div>
        </div>
        <div class="profile-user-comments showmore">
            <?= nl2br($this->TextEx->autoLink($user['TeamMember']['comment'])) ?>
        </div>
    </div>
</div>
<div class="profile-user-tab-group">
    <a class="profile-user-goal-tab <?= $this->request->params['action'] == 'view_goals' ? "profile-user-tab-active" : null ?>"
       href="<?= $this->Html->url(
           [
               'controller' => 'users',
               'action'     => 'view_goals',
               'user_id'    => $user['User']['id'],
           ]); ?>">
        <i class="fa fa-flag profile-user-tab-icon"></i>

        <p class="profile-user-tab-title">
            <?= h(__('Goal')) ?>
        </p>
    </a>
    <a class="profile-user-action-tab <?= $this->request->params['action'] == 'view_actions' ? "profile-user-tab-active" : null ?>"
       href="<?= $this->Html->url(
           [
               'controller' => 'users',
               'action'     => 'view_actions',
               'user_id'    => $user['User']['id'],
               'page_type'  => 'image',
           ]); ?>">
        <i class="fa fa-check-circle profile-user-tab-icon"></i>

        <p class="profile-user-tab-title">
            <?= h(__('Action')) ?>
        </p>
    </a>
    <a class="profile-user-post-tab <?= $this->request->params['action'] == 'view_posts' ? "profile-user-tab-active" : null ?>"
       href="<?= $this->Html->url(
           [
               'controller' => 'users',
               'action'     => 'view_posts',
               'user_id'    => $user['User']['id'],
           ]); ?>">
        <i class="fa fa-comment-o profile-user-tab-icon"></i>

        <p class="profile-user-tab-title">
            <?= h(__('Posts')) ?>
        </p>
    </a>
    <a class="profile-user-status-tab <?= $this->request->params['action'] == 'view_info' ? "profile-user-tab-active" : null ?>"
       href="<?= $this->Html->url(
           [
               'controller' => 'users',
               'action'     => 'view_info',
               'user_id'    => $user['User']['id'],
           ]) ?>">
        <i class="fa fa-user profile-user-tab-icon"></i>

        <p class="profile-user-tab-title">
            <?= h(__('Basic Information')) ?>
        </p>
    </a>
</div>


<?php
/**
 * @var $post
 * @var $site_info
 * @var $title_max_length
 * @var $description_max_length
 * @var $img_src
 */
$title_max_length = isset($title_max_length) ? $title_max_length : 50;
$description_max_length = isset($description_max_length) ? $description_max_length : 110;
$img_src = isset($img_src) ? $img_src : '';

// type が存在しないものは外部リンクとする（古いデータ対応）
if (!isset($site_info['type'])) {
    $site_info['type'] = 'external';
}

// 内部 OGP の「マイページ」「投稿」「アクション」の場合に使用するユーザーローカル名
$local_username = null;
if (isset($site_info['type']) && (
        $site_info['type'] == 'user' ||
        $site_info['type'] == 'post_normal' ||
        $site_info['type'] == 'post_action')
) {
    if (isset($site_info['user_local_names'][$this->Session->read('Auth.User.language')])) {
        $local_username = $site_info['user_local_names'][$this->Session->read('Auth.User.language')]['local_username'];
    }
}
?>
<?php if (isset($site_info)): ?>
    <?php if (isset($site_info['is_editing']) && $site_info['is_editing'] === true) : ?>
        <a href="#" class="font_lightgray comment-ogp-close"><i class="fa fa-times js-ogp-close"></i></a>
    <?php endif ?>
    <div class="col pt_10px js-ogp-box"
    <?php if (isset($site_info['is_editing']) && !empty($site_info['is_editing']) && !empty($comment_id)): ?>
        id="CommentOgpEditBox_<?= $comment_id ?>"
    <?php elseif (!empty($comment_id)): ?>
        id="CommentOgpBox_<?= $comment_id ?>"
    <?php endif; ?> >
        <a href="<?= isset($site_info['url']) ? $site_info['url'] : null ?>" target="blank"
           onclick="window.open(this.href,'_system');return false;"
           class="no-line font_verydark">
            <div class="site-info bd-radius_4px">
                <div class="media">
                    <div class="pull-left">
                        <?php if ($img_src): ?>
                            <?=
                            $this->Html->image('pre-load.svg', [
                                'class'         => 'lazy media-object',
                                'data-original' => $img_src,
                                'width'         => '80px',
                                'height'        => '80px',
                                'error-img'     => "/img/no-image-link.png",
                            ])
                            ?>
                        <?php elseif (isset($site_info['image']) && $site_info['image']): ?>
                            <?= $this->Html->image($site_info['image'], [
                                'class' => 'media-object',
                                'width' => '80px',
                            ]) ?>
                        <?php else: ?>
                            <?= $this->Html->image("/img/no-image-link.png", [
                                'class' => 'media-object',
                                'width' => '80px',
                            ]) ?>
                        <?php endif ?>
                    </div>
                    <div class="media-body" data-type="<?= $site_info['type']; ?>"
                         data-site-name="<?= isset($site_info['site_name'])? $site_info['site_name'] : "" ?>">
                        <h4 class="media-heading font_18px">
                            <?php if (isset($site_info['title'])): ?>
                                <?php
                                $_title = $site_info['title'];
                                // 内部 OGP の「マイページ」の場合はユーザー名を表示するので、ローカル名で上書きする
                                if ($site_info['type'] == 'user' && $local_username) {
                                    $_title = $local_username;
                                }
                                ?>
                                <?= mb_strimwidth(h($_title), 0, $title_max_length, "...") ?>
                            <?php endif ?>
                        </h4>

                        <?php
                        // 外部 OGP の場合、URL を表示
                        if ($site_info['type'] == 'external'): ?>
                            <p class="font_11px media-url"><?= isset($site_info['url']) ? h($site_info['url']) : null ?></p>
                        <?php endif ?>

                        <?php if (isset($site_info['description'])): ?>
                            <?php
                            $_desc = $site_info['description'];
                            // 内部 OGP の「投稿」「アクション」の場合はユーザー名を表示するので、ローカル名で上書きする
                            if (($site_info['type'] == 'post_normal' || $site_info['type'] == 'post_action') && $local_username) {
                                $_desc = $local_username;
                            }
                            ?>
                            <div class="font_12px site-info-txt">
                                <?= mb_strimwidth(h($_desc), 0, $description_max_length, "...") ?>
                            </div>
                        <?php endif; ?>

                        <?php
                        // 内部 OGP の場合、ページ種類を表示
                        if ($site_info['type'] != 'external'): ?>
                            <p class="font_11px media-url mt_3px">
                                <?php if (
                                    $site_info['type'] == 'post_normal' ||
                                    $site_info['type'] == 'post_create_goal' ||
                                    $site_info['type'] == 'post_kr_complete' ||
                                    $site_info['type'] == 'post_goal_complete' ||
                                    $site_info['type'] == 'post_create_circle'
                                ): ?>
                                    <i class="fa fa-comment-o"></i> <?= __('Posts') ?>
                                <?php elseif ($site_info['type'] == 'post_action'): ?>
                                    <i class="fa fa-check-circle"></i> <?= __('Action') ?>
                                <?php elseif ($site_info['type'] == 'circle'): ?>
                                    <i class="fa fa-circle-o"></i> <?= __('Circle') ?>
                                <?php elseif ($site_info['type'] == 'goal'): ?>
                                    <i class="fa fa-flag"></i> <?= __('Goal') ?>
                                <?php elseif ($site_info['type'] == 'team_vision'): ?>
                                    <i class="fa fa-rocket"></i> <?= __('Team Vision') ?>
                                <?php elseif ($site_info['type'] == 'group_vision'): ?>
                                    <i class="fa fa-plane"></i> <?= __('Group Vision') ?>
                                <?php elseif ($site_info['type'] == 'user'): ?>
                                    <i class="fa fa-user"></i> <?= __('Members') ?>
                                <?php endif ?>
                            </p>
                        <?php endif ?>
                    </div>
                </div>
            </div>
        </a>
    </div>
<?php endif ?>

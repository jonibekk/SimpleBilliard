<?php
/**
 * @var $post
 * @var $site_info
 */
?>
<?php if (isset($site_info)): ?>
    <?php
    // 内部 OGP の「マイページ」「投稿」「アクション」の場合に使用するユーザーローカル名
    $local_username = null;
    if (isset($site_info['type']) && (
            $site_info['type'] == 'user' ||
            $site_info['type'] == 'post' ||
            $site_info['type'] == 'action')
    ) {
        if (isset($site_info['user_local_names'][$this->Session->read('Auth.User.language')])) {
            $local_username = $site_info['user_local_names'][$this->Session->read('Auth.User.language')]['local_username'];
        }
    }
    ?>
    <div class="col col-xxs-12 pt_10px">
        <a href="<?= isset($site_info['url']) ? $site_info['url'] : null ?>" target="blank"
           onclick="window.open(this.href,'_system');return false;"
           class="no-line font_verydark">
            <div class="site-info bd-radius_4px">
                <div class="media">
                    <div class="pull-left">
                        <?php if (isset($post)): ?>
                            <?=
                            $this->Html->image('ajax-loader.gif', [
                                'class'         => 'lazy media-object',
                                'data-original' => $this->Upload->uploadUrl($post,
                                                                            "Post.site_photo",
                                                                            ['style' => 'small']),
                                'width'         => '80px',
                                'error-img'     => "/img/no-image-link.png",
                            ])
                            ?>
                        <?php elseif ($site_info['image']): ?>
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
                    <div class="media-body">
                        <h4 class="media-heading font_18px">
                            <?php if (isset($site_info['title'])): ?>
                                <?php
                                $_title = $site_info['title'];
                                // 内部 OGP の「マイページ」の場合はユーザー名を表示するので、ローカル名で上書きする
                                if ($site_info['type'] == 'user' && $local_username) {
                                    $_title = $local_username;
                                }
                                ?>
                                <?= mb_strimwidth(h($_title), 0, 50, "...") ?>
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
                            if (($site_info['type'] == 'post' || $site_info['type'] == 'action') && $local_username) {
                                $_desc = $local_username;
                            }
                            ?>
                            <div class="font_12px site-info-txt">
                                <?= mb_strimwidth(h($_desc), 0, 110, "...") ?>
                            </div>
                        <?php endif; ?>

                        <?php
                        // 内部 OGP の場合、ページ種類を表示
                        if ($site_info['type'] != 'external'): ?>
                            <p class="font_11px media-url mt_3px">
                                <?php if ($site_info['type'] == 'post'): ?>
                                    <i class="fa fa-comment-o"></i> <?= __d('gl', '投稿') ?>
                                <?php elseif ($site_info['type'] == 'action'): ?>
                                    <i class="fa fa-check-circle"></i> <?= __d('gl', 'アクション') ?>
                                <?php elseif ($site_info['type'] == 'circle'): ?>
                                    <i class="fa fa-circle-o"></i> <?= __d('gl', 'サークル') ?>
                                <?php elseif ($site_info['type'] == 'goal'): ?>
                                    <i class="fa fa-flag"></i> <?= __d('gl', 'ゴール') ?>
                                <?php elseif ($site_info['type'] == 'team_vision'): ?>
                                    <i class="fa fa-rocket"></i> <?= __d('gl', 'チームビジョン') ?>
                                <?php elseif ($site_info['type'] == 'group_vision'): ?>
                                    <i class="fa fa-plane"></i> <?= __d('gl', 'グループビジョン') ?>
                                <?php elseif ($site_info['type'] == 'user'): ?>
                                    <i class="fa fa-user"></i> <?= __d('gl', 'ユーザー') ?>
                                <?php endif ?>
                            </p>
                        <?php endif ?>
                    </div>
                </div>
            </div>
        </a>
    </div>
<?php endif ?>
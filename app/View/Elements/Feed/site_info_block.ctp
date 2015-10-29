<?php
/**
 * @var $post
 * @var $site_info
 */
?>
<?php if (isset($site_info)): ?>
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
                                'class'     => 'media-object',
                                'width'     => '80px',
                            ]) ?>
                        <?php else: ?>
                            <?= $this->Html->image("/img/no-image-link.png", [
                                'class' => 'media-object',
                                'width' => '80px',
                            ]) ?>
                        <?php endif ?>
                    </div>
                    <div class="media-body">
                        <h4 class="media-heading font_18px"><?= isset($site_info['title']) ? mb_strimwidth(h($site_info['title']),
                                                                                                           0,
                                                                                                           50,
                                                                                                           "...") : null ?></h4>

                        <p class="font_11px media-url"><?= isset($site_info['url']) ? h($site_info['url']) : null ?></p>
                        <?php if (isset($site_info['description'])): ?>
                            <div class="font_12px site-info-txt">
                                <?= mb_strimwidth(h($site_info['description']), 0, 110, "...") ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </a>
    </div>
<?php endif ?>
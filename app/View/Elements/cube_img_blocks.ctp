<?php
/**
 * Created by PhpStorm.
 * User: bigplants
 * Date: 7/16/15
 * Time: 1:45 PM
 *
 * @var $posts
 * @var $model
 */
?>
<?= $this->App->viewStartComment() ?>
<?php if (isset($posts) && !empty($posts)): ?>
    <div class="cube-img-column">
        <?php foreach ($posts as $post): ?>
            <div class="cube-img-column-frame">
                <a href="<?= $this->Html->url([
                    'controller' => 'posts',
                    'action'     => 'feed',
                    'post_id'    => $post['Post']['id']
                ]) ?>"
                <?php if (Hash::get($post, 'ActionResult.ActionResultFile.0.AttachedFile')): ?>
                    <!-- アクション画像がある場合 -->
                    <?= $this->Html->image('svg/pre-load.svg',
                        [
                            'class'         => 'cube-img-blocks-img lazy',
                            'data-original' => $this->Upload->uploadUrl($post['ActionResult']['ActionResultFile'][0]['AttachedFile'],
                                "AttachedFile.attached",
                                ['style' => 'small']),
                        ]
                    );
                    ?>
                <?php else: ?>
                    <!-- アクション画像がない場合 -->
                    <?php for ($i = 1; $i <= 5; $i++): ?>
                        <?php
                        if (!empty($post['ActionResult']["photo{$i}_file_name"]) || $i == 5) {
                            echo $this->Html->image('svg/pre-load.svg',
                                [
                                    'class'         => 'lazy img-responsive',
                                    'width'         => '186px',
                                    'height'        => '186px',
                                    'data-original' => $this->Upload->uploadUrl($post,
                                        "ActionResult.photo$i",
                                        ['style' => 'small']),
                                ]
                            );
                            break;
                        }
                        ?>
                    <?php endfor; ?>
                <?php endif; ?>
                </a>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
<?= $this->App->viewEndComment() ?>

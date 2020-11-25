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
        <?php foreach ($posts as $post): ?>
            <div class="cube-img-block action-image">
                <a href="<?= "/posts/" . $post['Post']['id'] ?>"
                <?php if (Hash::get($post, 'ActionResult.ActionResultFile.0.AttachedFile')): ?>
                    <!-- アクション画像がある場合 -->
                    <?= $this->Html->image($this->Upload->uploadUrl($post['ActionResult']['ActionResultFile'][0]['AttachedFile'],"AttachedFile.attached",['style' => 'small']),
                        [
                            'class'         => 'cube-img-blocks-img',
                        ]
                    );
                    ?>
                <?php else: ?>
                    <!-- アクション画像がない場合 -->
                    <?php for ($i = 1; $i <= 5; $i++): ?>
                        <?php
                        if (!empty($post['ActionResult']["photo{$i}_file_name"]) || $i == 5) {
                            echo $this->Html->image('pre-load.svg',
                                [
                                    'class'         => 'img-responsive',
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
<?php endif; ?>
<?= $this->App->viewEndComment() ?>

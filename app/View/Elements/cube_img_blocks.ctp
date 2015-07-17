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
<!-- START app/View/Elements/cube_img_blocks.ctp -->
<?php if (isset($posts) && !empty($posts)): ?>
    <?php foreach ($posts as $post): ?>
        <div class="col col-xxs-4">
            <a href="<?= $this->Html->url(['controller' => 'posts', 'action' => 'feed', 'post_id' => $post['Post']['id']]) ?>"
               title="<?= $post['ActionResult']['name'] ?>">
                <?=
                $this->Html->image('ajax-loader.gif',
                                   [
                                       'class'         => 'lazy img-responsive',
                                       'data-original' => $this->Upload->uploadUrl($post, 'ActionResult.photo1',
                                                                                   ['style' => 'small']),
                                   ]
                )
                ?>
            </a>
        </div>
    <?php endforeach; ?>
<?php endif; ?>
<!-- END app/View/Elements/cube_img_blocks.ctp -->

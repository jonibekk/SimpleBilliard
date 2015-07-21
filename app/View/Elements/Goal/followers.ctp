<?php
/**
 * @var $followers
 */
?>
<?php if ($followers): ?>
    <!-- START app/View/Elements/Goal/followers.ctp -->
    <?php foreach ($followers as $follower): ?>
        <div class="col col-xxs-12">
            <div style="border:3px #ccc solid; height: 67px;">
                <?=
                $this->Upload->uploadImage($follower['User'], 'User.photo', ['style' => 'small'],
                                           ['style' => 'width:64px; float:left; border-right:3px #ccc solid'])
                ?>
                <div class="font_bold" style="padding:3px;float:left">
                    <?= h($follower['User']['display_username']) ?><br>
                    <?= h($follower['Group']['name']) ?>
                </div>
            </div>
        </div>
    <?php endforeach ?>
    <!-- END app/View/Elements/Goal/followers.ctp -->
<? endif ?>

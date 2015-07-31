<?php
/**
 * @var $members
 */
?>
<?php if ($members): ?>
    <!-- START app/View/Elements/Goal/members.ctp -->
    <?php foreach ($members as $member): ?>
        <div class="goal-detail-member-card">
            <div style="height: 100px;">
                <?=
                $this->Upload->uploadImage($member['User'], 'User.photo', ['style' => 'small'],
                                           ['class' => 'img-circle',
                                            'style' => 'width:56px; float:left;'])
                ?>
                <div class="goal-detail-member-info">
                    <span class="goal-detail-member-name"><?= h($member['User']['display_username']) ?></span>
                    <?php if ($member['Collaborator']['type'] == Collaborator::TYPE_OWNER): ?>
                        <span class="goal-detail-member-owner">
                            <i class="fa fa-star"></i>
                        </span>
                    <?php endif ?>
                    <br>
                    <span class="font_bold"><?= h($member['Collaborator']['role']) ?></span><br>
                    <?= h($member['Collaborator']['description']) ?><br>
                </div>
            </div>
        </div>
    <?php endforeach ?>
    <!-- END app/View/Elements/Goal/members.ctp -->
<? endif ?>

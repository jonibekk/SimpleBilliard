<div class="panel panel-default">
    <div class="panel-heading">
        <?= __d('gl', "すべてのメッセージ") ?>
    </div>
    <div class="panel-body panel-body-notify-page">
        <?php foreach ($message_list as $key => $item) { ?>
            <ul class="notify-page-cards" role="menu">
                <li class="notify-card-list">
                    <a href="/posts/message#/<?php echo $item['Post']['id']; ?>" class="col col-xxs-12 notify-card-link" id="notifyCard">
                        <?php echo
                        $this->Html->image(
                            $this->Upload->uploadUrl(
                                $item,
                                'User.photo',
                                ['style' => 'medium_large']
                            ),
                            array(
                                'class' => array('pull-left notify-card-pic')
                            )
                        );
                        ?>
                        <div class="notify-contents">
                            <div class="col col-xxs-12 notify-card-head">
                                <span class="font-heading">
                                    <?php echo $item['User']['display_username']; ?>
                                    <?php if (count($item['PostShareUser']) > 0) echo '+'. count($item['PostShareUser'])?>
                                </span>
                            </div>
                            <div class="col col-xxs-12 notify-text notify-line-number notify-card-text"
                                 id="CommentTextBody_67">
                                <i class="fa font_bold"></i> <?php echo $item['Post']['body'] ?>
                            </div>
                            <p class="notify-card-aside"><?php echo $this->TimeEx->elapsedTime(h($item['Post']['created'])); ?></p>
                        </div>
                    </a>
                </li>
                <li class="divider notify-divider"></li>
            </ul>
        <? } ?>
    </div>
</div>

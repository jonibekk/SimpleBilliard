<li>
    <a href="<?= $postUrl ?>" class="col col-xxs-12 pt_4px" style="display:block;padding-top:10px;">
        <?=
        $this->Html->image(
            $this->Upload->uploadUrl(
                $this->Session->read('Auth.User'),
               'User.photo',
               ['style' => 'small']
            ),
            array(
                'class' => array('img-circle', 'pull-left')
            )
        );
        ?>
        <div class="comment-body">
            <div class="col col-xxs-12 comment-text comment-user">
                <div class="mb_2px lh_12px">
                    <span class="font_bold font_verydark"><?= $userName ?></span>
                    からコメントがありました。
                </div>
            </div>
            <div class="col col-xxs-12 showmore-comment comment-text feed-contents comment-contents font_verydark box-align" id="CommentTextBody_67" style="
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
                width: 100%;">
                <i class="fa fa-comment-o"></i>「<?= $comment ?>」
            </div>
        </div>
    </a>
</li>
<li class="divider" style="clear:both"></li>

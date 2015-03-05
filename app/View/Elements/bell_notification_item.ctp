<!-- START app/View/Elements/bell_notification.ctp -->
<li class="divider notify-divider"></li>
<li class="notify-card-list">
    <a href="<?= $postUrl ?>" class="col col-xxs-12 notify-card" id="notifyCard">
        <?=
        $this->Html->image(
            $this->Upload->uploadUrl(
                $this->Session->read('Auth.User'),
               'User.photo',
               ['style' => 'medium_large']
            ),
            array(
                'class' => array('pull-left notify-icon')
            )
        );
        ?>
        <div class="comment-body col-xxs-9 notify-contents">
            <div class="col col-xxs-12 comment-text comment-user">
                <div class="mb_2px lh_12px">
                    <?= __d('notify',"%sからコメントがありました。",'<span class="font_bold font_verydark">' . $userName . '</span>')?>
                </div>
            </div>

            <div class="col col-xxs-12 showmore-comment comment-text feed-contents comment-contents font_verydark box-align notify-text notify-line-number" id="CommentTextBody_67">
                <i class="fa fa-comment-o disp_i"></i>「<?= $comment ?>」
            </div>
        </div>
    </a>
</li>
<!-- END app/View/Elements/bell_notification.ctp -->

<?= $this->App->viewStartComment() ?>
<div
    class="col feed-contents post-contents <?= viaIsSet($long_text) ? "showmore-circle" : "showmore" ?> font_14px font_verydark box-align"
    id="PostTextBody_<?= $post['Post']['id'] ?>">
    <?php $mentions = $this->Mention->getMyMentions($post['Post']['body'], $my_id, $my_team_id) ?>
    <?php if (($post['Post']['type'] == Post::TYPE_NORMAL) || ($post['Post']['type'] == Post::TYPE_MESSAGE)): ?>
        <?= $this->Mention->replaceMention(nl2br($this->TextEx->autoLink($post['Post']['body'])), $mentions) ?>
    <?php elseif ($post['Post']['type'] == Post::TYPE_ACTION): ?>
        <i class="fa fa-check-circle disp_i"></i>&nbsp;<?= nl2br($this->TextEx->autoLink($post['ActionResult']['name'])) ?>
    <?php elseif ($post['Post']['type'] == Post::TYPE_KR_COMPLETE): ?>
        <i class="fa fa-key disp_i"></i>&nbsp;<?= __("Achieved %s!",
            h($post['KeyResult']['name'])) ?>
    <?php elseif ($post['Post']['type'] == Post::TYPE_GOAL_COMPLETE): ?>
        <i class="fa fa-flag disp_i"></i>&nbsp;<?= __("Achieved %s!", h($post['Goal']['name'])) ?>
    <?php else: ?>
        <?= Post::$TYPE_MESSAGE[$post['Post']['type']] ?>
    <?php endif; ?>
</div>
<?= $this->App->viewEndComment() ?>

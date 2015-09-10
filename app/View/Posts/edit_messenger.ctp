<?php
/**
 *
 */
?>
<!-- START app/View/Post/post_edit.ctp -->
<div class="col-sm-8 col-sm-offset-2">
    <div class="panel panel-default">
        <div class="panel-body">
            <?php  echo $this->element('Feed/add_messenger', [
                'common_form_type'     => 'message',
                'common_form_only_tab' => 'message'
            ]) ?>
        </div>
    </div>
</div>
<!-- END app/View/Post/post_edit.ctp -->
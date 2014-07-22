<?php
/**
 * Created by PhpStorm.
 * User: bigplants
 * Date: 7/22/14
 * Time: 11:05 AM
 *
 * @var $index
 */
?>
<div class="form-group">
    <div class="fileinput_post_comment fileinput-new" data-provides="fileinput">
        <div class="fileinput-preview thumbnail nailthumb-container" data-trigger="fileinput"
             style="width: 50px; height: 50px;">
        </div>
        <div>
                        <span class="btn-file">
                            <?=
                            $this->Form->input('photo' . $index,
                                               ['type'         => 'file',
                                                'label'        => false,
                                                'div'          => false,
                                                'css'          => false,
                                                'wrapInput'    => false,
                                                'errorMessage' => false,
                                                ''
                                               ]) ?>
                        </span>
        </div>
    </div>
</div>
<div class="tab-pane fade" id="ActionForm">
    <?php if (count($canActionGoals) == 0): ?>
        <div class="post-panel-body plr_11px ptb_7px">
            <div class="alert alert-warning" role="alert">
                <?= __('There is no goal that you can take action.') ?>
                <a href="/goals/create/step1"
                   class="alert-link"><?= __('Create a goal') ?></a>
            </div>
        </div>
    <?php else: ?>
        <?= $this->Form->create('ActionResult', [
            'url'              => $is_edit_mode
                ? [
                    'controller'       => 'goals',
                    'action'           => 'edit_action',
                    'action_result_id' => $this->request->data['ActionResult']['id']
                ]
                : ['controller' => 'goals', 'action' => 'add_completed_action'],
            'inputDefaults'    => [
                'div'       => 'form-group',
                'label'     => false,
                'wrapInput' => '',
                'class'     => 'form-control',
            ],
            'data-is-edit' => $is_edit_mode,
            'id'               => 'CommonActionDisplayForm',
            'type'             => 'file',
            'novalidate'       => true,
            'class'            => 'form-feed-notify'
        ]); ?>
        <div class="post-panel-body plr_11px ptb_7px">
            <a href="#"
               id="ActionImageAddButton"
               class="post-action-image-add-button <?php
               // 投稿編集モードの場合は画像選択の画面をスキップする
               if ($is_edit_mode && $common_form_type == 'action'): ?>
                        skip
                        <?php endif ?>"
               target-id="CommonActionSubmit,WrapActionFormName,WrapCommonActionGoal,CommonActionFooter,CommonActionFormShowOptionLink,ActionUploadFileDropArea"
               delete-method="hide">
                        <span class="action-image-add-button-text"><i
                                class="fa fa-image action-image-add-button-icon"></i> <span><?= __(
                                    'Upload an image as your action') ?></span></span>
            </a>
        </div>

        <div id="ActionUploadFilePhotoPreview" class="pull-left action-upload-main-image-preview"></div>

        <div id="WrapActionFormName"
             class="panel-body action-form-panel-body none pull-left action-input-name">
            <?=
            $this->Form->input('name', [
                'id'                           => 'CommonActionName',
                'label'                        => false,
                'type'                         => 'textarea',
                'wrap'                         => 'soft',
                'rows'                         => 1,
                'required'                     => true,
                'placeholder'                  => __("Write an action..."),
                'class'                        => 'form-control',
                'data-bv-notempty-message'     => __("Input is required."),
                'data-bv-stringlength'         => 'true',
                'data-bv-stringlength-max'     => 10000,
                'data-bv-stringlength-message' => __("It's over limit characters (%s).", 10000),
            ])
            ?>
        </div>
        <!-- 目印 -->
        <div id="ActionUploadFileDropArea" class="action-upload-file-drop-area">
            <?php if (!$is_edit_mode): ?>
                <div class="panel-body action-form-panel-body form-group none" id="WrapCommonActionGoal">
                    <div class="input-group feed-action-goal-select-wrap">
                        <span class="input-group-addon" id=""><i class="fa fa-flag"></i></span>
                        <?=
                        $this->Form->input('goal_id', [
                            'label'                    => false,
                            'div'                      => false,
                            'required'                 => true,
                            'data-bv-notempty-message' => __("Input is required."),
                            'class'                    => 'form-control js-change-goal',
                            'id'                       => 'GoalSelectOnActionForm',
                            'options'                  => $canActionGoals,
                            'target-value'             =>
                                isset($this->request->data['ActionResult']['key_result_id'])
                                    ? $this->request->data['ActionResult']['key_result_id']
                                    : "",
                            'ajax-url'                 =>
                                $this->Html->url([
                                    'controller' => 'goals',
                                    'action'     => 'ajax_get_kr_list',
                                    'goal_id'    => ""
                                ]),
                        ])
                        ?>
                    </div>
                </div>
                <ul class="action-kr-progress-edit" id="SelectKrProgress">
                </ul>
            <?php endif; ?>


            <?php
            // 新規登録時のみ表示
            if (!$is_edit_mode): ?>
                <a href="#" class="graylink-dark- target-show click-this-remove none"
                   target-id="ActionFormOptionFields"
                   id="CommonActionFormShowOptionLink">
                    <div class="panel-body action-form-panel-body font_11px font_lightgray"
                         id="CommonActionFormShare">
                        <p class="text-center"><?= __("View options") ?></p>

                        <p class="text-center"><i class="fa fa-chevron-down"></i></p>
                    </div>
                </a>

                <div id="ActionFormOptionFields" class="none">
                    <div class="panel-body action-form-panel-body" id="CommonActionFormShare">
                        <div class="col col-xxs-12 col-xs-12 post-share-range-list"
                             id="CommonActionShareInputWrap">
                            <div class="input-group action-form-share-input-group">
                                <span class="input-group-addon" id=""><i class="fa fa-bullhorn"></i></span>

                                <div class="form-control">
                                    <?=
                                    $this->Form->hidden('share',
                                        [
                                            'id'    => 'select2ActionCircleMember',
                                            'value' => "",
                                            'style' => "width: 100%",
                                        ]) ?>
                                    <?php $this->Form->unlockField('ActionResult.share') ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif ?>

            <div id="ActionUploadFilePreview" class="action-upload-file-preview">
            </div>
            <div class="post-panel-footer none" id="CommonActionFooter">
                <div class="font_12px" id="CommonActionFormFooter">
                    <a href="#" class="link-red" id="ActionFileAttachButton">
                        <button type="button" class="btn pull-left photo-up-btn"><i
                                class="fa fa-paperclip post-camera-icon"></i>
                        </button>
                    </a>

                    <div class="row form-horizontal form-group post-share-range" id="CommonActionShare">
                        <?=
                        $this->Form->submit(__($is_edit_mode ? "保存する" : "アクション登録"),
                            [
                                'class' => 'btn btn-primary pull-right post-submit-button',
                                'id'    => 'CommonActionSubmit'
                            ]) ?>
                    </div>
                </div>
            </div>
        </div>
        <?php if ($is_edit_mode): ?>
            <?php if (isset($this->request->data['ActionResultFile']) && is_array($this->request->data['ActionResultFile'])): ?>
                <?php foreach ($this->request->data['ActionResultFile'] as $file): ?>
                    <?= $this->Form->hidden('file_id', [
                        'id'        => 'AttachedFile_' . $file['AttachedFile']['id'],
                        'name'      => 'data[file_id][]',
                        'value'     => $file['AttachedFile']['id'],
                        'data-url'  => $this->Upload->uploadUrl($file, 'AttachedFile.attached',
                            ['style' => 'small']),
                        'data-name' => $file['AttachedFile']['attached_file_name'],
                        'data-size' => $file['AttachedFile']['file_size'],
                        'data-ext'  => $file['AttachedFile']['file_ext'],
                    ]); ?>
                <?php endforeach ?>
            <?php endif; ?>
        <?php endif ?>
        <?php $this->Form->unlockField('socket_id') ?>
        <?php $this->Form->unlockField('file_id') ?>
        <?php $this->Form->unlockField('ActionResult.file_id') ?>
        <?php $this->Form->unlockField('deleted_file_id') ?>

        <?= $this->Form->end() ?>
    <?php endif; ?>
</div>

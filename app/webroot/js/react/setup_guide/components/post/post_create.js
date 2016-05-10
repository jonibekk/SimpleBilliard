import React, { PropTypes } from 'react'
import ReactDOM from 'react-dom'
import { Link } from 'react-router'

export default class PostCreate extends React.Component {
  constructor(props) {
    super(props)
  }
  componentDidMount() {
    ReactDOM.findDOMNode(this.refs.ActionImageAddButton).setAttribute("target-id", "CommonActionSubmit,WrapActionFormName,WrapCommonActionGoal,CommonActionFooter,CommonActionFormShowOptionLink,ActionUploadFileDropArea")
    ReactDOM.findDOMNode(this.refs.ActionImageAddButton).setAttribute("delete-method", "hide")
  }
  render() {
    return (
      <div>
        <div className="setup-pankuzu font_18px">
          {__("Set up Goalous")} <i className="fa fa-angle-right" aria-hidden="true"></i> {__('Post to a circle')}
        </div>
        <div className="panel panel-default global-form" id="GlobalForms">
          <div className="post-panel-heading ptb_7px plr_11px">
            <ul className="feed-switch clearfix plr_0px" id="CommonFormTabs">
              <li className="switch-action">
                <Link to="#PostForm"
                   className="switch-post-anchor click-target-focus">
                   <i className="fa fa-comment-o"></i>{__("Posts")}
                </Link>
                <span className="switch-arrow"></span>
              </li>
            </ul>
          </div>
          <div class="tab-pane fade" id="PostForm">
              <?=
              $this->Form->create('Post', [
                  'url'           => $is_edit_mode && isset($this->request->data['Post']['id'])
                      ? ['controller' => 'posts', 'action' => 'post_edit', 'post_id' => $this->request->data['Post']['id']]
                      : ['controller' => 'posts', 'action' => 'add'],
                  'inputDefaults' => [
                      'div'       => 'form-group',
                      'label'     => false,
                      'wrapInput' => '',
                      'class'     => 'form-control',
                  ],
                  'id'            => 'PostDisplayForm',
                  'type'          => 'file',
                  'novalidate'    => true,
                  'class'         => 'form-feed-notify'
              ]); ?>
              <div class="post-panel-body plr_11px ptb_7px">
                  <?=
                  $this->Form->input('body', [
                      'id'                           => 'CommonPostBody',
                      'label'                        => false,
                      'type'                         => 'textarea',
                      'wrap'                         => 'soft',
                      'rows'                         => 1,
                      'placeholder'                  => __("Write something..."),
                      'class'                        => 'form-control tiny-form-text-change post-form feed-post-form box-align change-warning',
                      "required"                     => true,
                      'data-bv-notempty-message'     => __("Input is required."),
                      'data-bv-stringlength'         => 'true',
                      'data-bv-stringlength-max'     => 10000,
                      'data-bv-stringlength-message' => __("It's over limit characters (%s).", 10000),
                  ])
                  ?>

                  <?= $this->Form->hidden('site_info_url', ['id' => 'PostSiteInfoUrl']) ?>
                  <?php $this->Form->unlockField('Post.site_info_url') ?>
                  <?= $this->Form->hidden('redirect_url', ['id' => 'PostRedirectUrl']) ?>
                  <?php $this->Form->unlockField('Post.redirect_url') ?>

                  <div id="PostOgpSiteInfo" class="post-ogp-site-info"></div>
                  <div id="PostUploadFilePreview" class="post-upload-file-preview"></div>
              </div>

              <?php
              // 新規登録時のみ表示
              if (!$is_edit_mode): ?>
                  <div class="panel-body post-share-range-panel-body" id="PostFormShare">

                      <?php
                      // 共有範囲「公開」のデフォルト選択
                      // 「チーム全体サークル」以外のサークルフィードページの場合は、対象のサークルIDを指定。
                      // それ以外は「チーム全体サークル」(public)を指定する。
                      $public_share_default = 'public';
                      if (isset($current_circle) && $current_circle['Circle']['public_flg'] && !$current_circle['Circle']['team_all_flg']) {
                          $public_share_default = "circle_" . $current_circle['Circle']['id'];
                      }

                      // 共有範囲「秘密」のデフォルト選択
                      // 秘密サークルのサークルフィードページの場合は、対象のサークルIDを指定する。
                      $secret_share_default = '';
                      if (isset($current_circle) && !$current_circle['Circle']['public_flg']) {
                          $secret_share_default = "circle_" . $current_circle['Circle']['id'];
                      }
                      ?>
                      <div class="col col-xxs-10 col-xs-10 post-share-range-list" id="PostPublicShareInputWrap"
                           <?php if ($secret_share_default) : ?>style="display:none"<?php endif ?>>
                          <?=
                          $this->Form->hidden('share_public', [
                              'id'    => 'select2PostCircleMember',
                              'value' => $public_share_default,
                              'style' => "width: 100%"
                          ]) ?>
                          <?php $this->Form->unlockField('Post.share_public') ?>
                      </div>
                      <div class="col col-xxs-10 col-xs-10 post-share-range-list" id="PostSecretShareInputWrap"
                           <?php if (!$secret_share_default) : ?>style="display:none"<?php endif ?>>
                          <?=
                          $this->Form->hidden('share_secret', [
                              'id'    => 'select2PostSecretCircle',
                              'value' => $secret_share_default,
                              'style' => "width: 100%;"]) ?>
                          <?php $this->Form->unlockField('Post.share_secret') ?>
                      </div>
                      <div class="col col-xxs-2 col-xs-2 text-center post-share-range-toggle-button-container">
                          <?= $this->Html->link('', '#', [
                              'id'                  => 'postShareRangeToggleButton',
                              'class'               => "btn btn-lightGray btn-white post-share-range-toggle-button",
                              'data-toggle-enabled' => (isset($current_circle)) ? '' : '1',
                          ]) ?>
                          <?= $this->Form->hidden('share_range', [
                              'id'    => 'postShareRange',
                              'value' => $secret_share_default ? 'secret' : 'public',
                          ]) ?>
                      </div>
                      <?php $this->Form->unlockField('Post.share_range') ?>
                  </div>
              <?php endif ?>

              <div class="post-panel-footer">
                  <div class="font_12px" id="PostFormFooter">
                      <a href="#" class="link-red" id="PostUploadFileButton">
                          <button type="button" class="btn pull-left photo-up-btn"><i
                                  class="fa fa-paperclip post-camera-icon"></i>
                          </button>
                      </a>

                      <div class="row form-horizontal form-group post-share-range" id="PostShare">
                          <?=
                          $this->Form->submit(__($is_edit_mode ? "Save" : "Post"),
                                              ['class'    => 'btn btn-primary pull-right post-submit-button',
                                               'id'       => 'PostSubmit',
                                               'disabled' => $is_edit_mode ? '' : 'disabled']) ?>
                      </div>
                  </div>
              </div>
              <?php if ($is_edit_mode): ?>
                  <?php if (isset($this->request->data['PostFile']) && is_array($this->request->data['PostFile'])): ?>
                      <?php foreach ($this->request->data['PostFile'] as $file): ?>
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
              <?php $this->Form->unlockField('Post.file_id') ?>
              <?php $this->Form->unlockField('deleted_file_id') ?>

              <?= $this->Form->end() ?>
          </div>
        </div>
      </div>
    )
  }
}

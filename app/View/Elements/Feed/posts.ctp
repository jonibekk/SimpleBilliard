<?php
/**
 * Created by PhpStorm.
 * User: daikihirakata
 * Date: 2014/07/06
 * Time: 1:03
 *
 * @var                    $posts
 * @var CodeCompletionView $this
 */
?>
<? if (!empty($posts)): ?>
    <? foreach ($posts as $post_key => $post): ?>
        <div class="panel panel-default">
            <div class="panel-body gl-feed">
                <div class="col col-xxs-12">
                    <?=
                    $this->Upload->uploadImage($post['User'], 'User.photo', ['style' => 'medium'],
                                               ['class' => 'gl-feed-img']) ?>
                    <div><?= h($post['User']['display_username']) ?></div>
                    <div><?= $this->TimeEx->datetimeNoYear(h($post['Post']['created'])) ?></div>
                </div>
                <div class="col col-xxs-12">
                    <?= nl2br($this->Text->autoLink(h($post['Post']['body']))) ?>
                </div>
                <div class="col col-xxs-12">
                    <a href="" class="">いいね！</a>&nbsp;<a href="">コメントする</a>
                </div>
            </div>
            <div class="panel-body gl-feed gl-comment-block">
                <!--                <div class="col col-xxs-12">-->
                <!--                    --><? //=
                //                    $this->Upload->uploadImage($post['User'], 'User.photo', ['style' => 'small'],
                //                                               ['class' => 'gl-comment-img'])
                ?>
                <!--                    <div class="gl-comment-body"><span>-->
                <? //= h($post['User']['display_username']) ?><!--</span>-->
                <!--                        --><? //= nl2br($this->Text->autoLink(h($post['Post']['body']))) ?>
                <!--                        <div>-->
                <!--                            --><? //= $this->TimeEx->datetimeNoYear(h($post['Post']['created'])) ?>
                <!--                            <a href="" class="">いいね！</a></div>-->
                <!--                    </div>-->
                <!--                </div>-->
                <div class="col col-xxs-12">
                    <?=
                    $this->Upload->uploadImage($this->Session->read('Auth.User'), 'User.photo', ['style' => 'small'],
                                               ['class' => 'gl-comment-img']) ?>
                    <div class="gl-comment-body">
                        <?=
                        $this->Form->create('Comment', [
                            'url'           => ['controller' => 'posts', 'action' => 'comment_add'],
                            'inputDefaults' => [
                                'div'       => 'form-group',
                                'label'     => false,
                                'wrapInput' => '',
                                'class'     => 'form-control'
                            ],
                            'class'         => '',
                            'novalidate'    => true,
                        ]); ?>
                        <?=
                        $this->Form->input('body', [
                            'label'          => false,
                            'type'           => 'textarea',
                            'rows'           => 1,
                            'placeholder'    => __d('gl', "コメントする"),
                            'class'          => 'form-control tiny-form-text',
                            'target_show_id' => "Comment_{$post['Post']['id']}",
                        ])
                        ?>
                        <?= $this->Form->hidden('post_id', ['value' => $post['Post']['id']]) ?>
                        <div class="" style="display: none" id="Comment_<?= $post['Post']['id'] ?>">
                        <?= $this->Form->submit(__d('gl', "コメントする"), ['class' => 'btn btn-primary pull-right']) ?>
                            <div class="clearfix"></div>
                        </div>
                        <?= $this->Form->end() ?>
                    </div>
                </div>
            </div>
        </div>
    <? endforeach ?>
<? endif ?>

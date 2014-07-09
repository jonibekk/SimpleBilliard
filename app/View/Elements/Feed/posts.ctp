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
    <? foreach ($posts as $post): ?>
        <div class="panel panel-default">
            <div class="panel-body">
                <div class="col col-xxs-2">
                    <?=
                    $this->Upload->uploadImage($post['User'], 'User.photo', ['style' => 'medium_large'],
                                               ['width' => '60px', 'height' => '60px']) ?>
                </div>
                <div class="col col-xxs-10">
                    <div class="col col-xxs-12">
                        <?= h($post['User']['display_username']) ?>
                    </div>
                    <div class="col col-xxs-12">
                        <?= $this->TimeEx->datetimeNoYear(h($post['Post']['created'])) ?>
                    </div>
                </div>
                <div class="col col-xxs-12">
                    <?= nl2br($this->Text->autoLink(h($post['Post']['body']))) ?>
                </div>
                <div class="col col-xxs-12">
                    <a href="" class="">いいね！</a>&nbsp;<a href="">コメントする</a>
                </div>
            </div>
        </div>
    <? endforeach ?>
<? endif ?>

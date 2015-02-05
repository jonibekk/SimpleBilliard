<?php
/**
 * Created by PhpStorm.
 * User: bigplants
 * Date: 5/22/14
 * Time: 6:47 PM
 *
 * @var CodeCompletionView $this
 * @var                    $current_circle
 * @var                    $my_member_status
 */
?>
<!-- START app/View/Elements/gl_common_js.ctp -->
<?
//echo $this->Html->script('jquery-2.1.0.min');
echo $this->Html->script('jquery-1.11.1.min');
echo $this->Html->script('bootstrap.min');
echo $this->Html->script('jasny-bootstrap.min');
echo $this->Html->script('bootstrapValidator.min');
echo $this->Html->script('bootstrap-switch.min');
echo $this->Html->script('bvAddition');
echo $this->Html->script('pnotify.custom.min');
echo $this->Html->script('jquery.nailthumb.1.1.min');
echo $this->Html->script('jquery.autosize.min');
echo $this->Html->script('jquery.lazy.min');
echo $this->Html->script('lightbox.min');
echo $this->Html->script('jquery.showmore.min');
echo $this->Html->script('ZeroClipboard.min');
echo $this->Html->script('placeholders.min');
echo $this->Html->script('customRadioCheck.min');
echo $this->Html->script('select2.min');
echo $this->Html->script('bootstrap-datepicker.min');
echo $this->Html->script('locales/bootstrap-datepicker.ja');
echo $this->Html->script('moment.min');
echo $this->Html->script('gl_basic');
?>
<script type="text/javascript">
    var cake = {};
    cake.message = {
        "a": "<?=__d('gl',"入力が途中です。このまま移動しますか？")?>",
        "b": "<?=__d('gl',"クリップボードに投稿URLをコピーしました。")?>",
        "c": "<?=__d('validate', '%2$d文字以上で入力してください。',"",8)?>",
        "d": "<?=__d('validate', "パスワードが一致しません。")?>",
        "e": "<?=__d('gl',"スペルを入力してください。")?>",
        "f": "<?=$this->Html->url(['controller'=>'users','action'=>'ajax_select2_get_users'])?>",
        "g": "<?=__d('gl',"自分のみ")?>",
        "h": "<?=__d('gl',"参加")?>",
        "i": "<?=__d('gl',"不参加")?>",
        "j": "<?=__d('gl',"該当なし")?>",
        "k": "<?=__d('gl',"あと")?>",
        "l": "<?=__d('gl',"文字入れてください")?>",
        "m": "<?=__d('gl',"検索文字列が")?>",
        "n": "<?=__d('gl',"文字長すぎます")?>",
        "o": "<?=__d('gl',"最多で")?>",
        "p": "<?=__d('gl',"項目までしか選択できません")?>",
        "q": "<?=__d('gl',"読込中･･･")?>",
        "r": "<?=__d('gl',"検索中･･･")?>",
        "s": "<?=__d('gl',"フォロー中")?>",
        "t": "<?=__d('gl',"フォロー")?>",
        "u": "<?=__d('gl',"エラーが発生しました。データ取得できません。")?>",
        "v": "<?=__d('gl',"クリップボードに投稿URLをコピーしました。")?>",
        "w": "<?=__d('gl', "もっと見る ▼") ?>",
        "x": "<?=__d('gl', "さらに以前の投稿を読み込む ▼") ?>",
        "y": "<?=__d('gl',"エラーが発生しました。データ取得できません。")?>",
        "z": "<?=__d('gl','これ以上のコメントがありません。')?>",
        "aa": "<?=__d('gl',"エラーが発生しました。データ取得できません。")?>",
        "bb": "<?=__d('gl',"エラーが発生しました。")?>",
        "cc": "<?=__d('gl',"もっと見る")?>",
        "dd": "<?=__d('gl',"閉じる")?>",
        "ee": "<?=__d('validate',"開始日が期限を過ぎています。")?>",
        "ff": "<?=__d('validate',"期限が開始日以前になっています。")?>"
    };
    cake.data = {
        "a": <?=isset($select2_default)?$select2_default:"[]"?>,
        "b": function (element, callback) {
            <?if(isset($current_circle)&&!empty($current_circle)):?>
                var data = [
                    {
                        id: "circle_<?=$current_circle['Circle']['id']?>",
                        text: "<?=h($current_circle['Circle']['name'])?>",
                        image: "<?=$this->Upload->uploadUrl($current_circle, 'Circle.photo', ['style' => 'small'])?>"
                    }
                ];
            <?else:?>
                var data = [
                    {
                        'id': 'public',
                        'text': "<?=__d('gl',"チーム全体")?>",
                        'image': "<?=isset($my_member_status)?$this->Upload->uploadUrl($my_member_status, 'Team.photo', ['style' => 'small']):null?>"
                    }
                ];
            <?endif;?>
                callback(data);
            }
    };
    cake.url = {
        "a": "<?=$this->Html->url(['controller'=>'users','action'=>'ajax_select2_get_users'])?>",
        "b": "<?=$this->Html->url(['controller'=>'circles','action'=>'ajax_select2_init_circle_members'])?>/",
        "c": "<?=$this->Html->url(['controller'=>'goals','action'=>'ajax_toggle_follow'])?>",
        "d": "<?=$this->Html->url(['controller'=>'posts','action'=>'ajax_post_like'])?>",
        "e": "<?=$this->Html->url(['controller'=>'posts','action'=>'ajax_comment_like'])?>"
    };


    <?if(isset($mode_view)):?>
    <?if($mode_view == MODE_VIEW_TUTORIAL):?>
    $("#modal_tutorial").modal('show');
    <?endif;?>
    <?endif;?>
</script>
<?= $this->Session->flash('click_event') ?>
<?
echo $this->Session->flash('pnotify');
//環境を識別できるようにリボンを表示
?>
<? if (ENV_NAME == "stg"): ?>
    <p class="ribbon ribbon-staging">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Staging</p>
<? elseif (ENV_NAME == "hotfix"): ?>
    <p class="ribbon ribbon-hotfix">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Hotfix</p>
<?
elseif (ENV_NAME == "local"): ?>
    <p class="ribbon ribbon-local">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Local</p>
<? endif; ?>
<!-- END app/View/Elements/gl_common_js.ctp -->

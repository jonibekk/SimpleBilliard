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
echo $this->Html->script('pusher.min');
echo $this->Html->script('gl_basic');
?>
<!--suppress JSDuplicatedDeclaration -->
<script type="text/javascript">
    var cake = {
        message: {
            validate: {
                a: "<?=__d('validate', '%2$d文字以上で入力してください。',"",8)?>",
                b: "<?=__d('validate', "パスワードが一致しません。")?>",
                c: "<?=__d('validate', "10MB以下かつJPG、PNG、GIFのいずれかの形式を選択して下さい。")?>",
                d: "<?=__d('validate', "利用規約に同意してください。")?>"
            },
            notice: {
                a: "<?=__d('gl',"入力が途中です。このまま移動しますか？")?>",
                b: "<?=__d('gl',"スペルを入力してください。")?>",
                c: "<?=__d('gl',"エラーが発生しました。データ取得できません。")?>",
                d: "<?=__d('gl',"エラーが発生しました。")?>",
                e: "<?=__d('validate',"開始日が期限を過ぎています。")?>",
                f: "<?=__d('validate',"期限が開始日以前になっています。")?>"
            },
            info: {
                a: "<?=__d('gl',"クリップボードに投稿URLをコピーしました。")?>",
                b: "<?=__d('gl',"読込中･･･")?>",
                c: "<?=__d('gl',"検索中･･･")?>",
                d: "<?=__d('gl',"フォロー中")?>",
                e: "<?=__d('gl', "もっと見る") ?>",
                f: "<?=__d('gl', "さらに以前の投稿を読み込む ▼") ?>",
                g: "<?=__d('gl','これ以上のコメントがありません。')?>",
                h: "<?=__d('gl',"閉じる")?>"
            }
        },
        word: {
            a: "<?=__d('gl',"自分のみ")?>",
            b: "<?=__d('gl',"参加")?>",
            c: "<?=__d('gl',"不参加")?>",
            d: "<?=__d('gl',"該当なし")?>",
            e: "<?=__d('gl',"あと")?>",
            f: "<?=__d('gl',"文字入れてください")?>",
            g: "<?=__d('gl',"検索文字列が")?>",
            h: "<?=__d('gl',"文字長すぎます")?>",
            i: "<?=__d('gl',"最多で")?>",
            j: "<?=__d('gl',"項目までしか選択できません")?>"
        },
        url: {
            a: "<?=$this->Html->url(['controller'=>'users','action'=>'ajax_select2_get_users'])?>",
            b: "<?=$this->Html->url(['controller'=>'circles','action'=>'ajax_select2_init_circle_members'])?>/",
            c: "<?=$this->Html->url(['controller'=>'goals','action'=>'ajax_toggle_follow'])?>",
            d: "<?=$this->Html->url(['controller'=>'posts','action'=>'ajax_post_like'])?>",
            e: "<?=$this->Html->url(['controller'=>'posts','action'=>'ajax_comment_like'])?>"
        },
        data: {
            a: <?=isset($select2_default)?$select2_default:"[]"?>,
            b: function (element, callback) {
                var data = [{
                    <?if(isset($current_circle)&&!empty($current_circle)):?>
                    id: "circle_<?=$current_circle['Circle']['id']?>",
                    text: "<?=h($current_circle['Circle']['name'])?>",
                    image: "<?=$this->Upload->uploadUrl($current_circle, 'Circle.photo', ['style' => 'small'])?>"
                    <?else:?>
                    id: 'public',
                    text: "<?=__d('gl',"チーム全体")?>",
                    image: "<?=isset($my_member_status)?$this->Upload->uploadUrl($my_member_status, 'Team.photo', ['style' => 'small']):null?>"
                    <?endif;?>
                }];
                callback(data);
            },
            c: <?=$my_channels_json?>,
            d: "<?=viaIsSet($feed_filter)?>",
            e: <?=MY_GOALS_DISPLAY_NUMBER?>,
            f: <?=MY_COLLABO_GOALS_DISPLAY_NUMBER?>,
            g: <?=MY_FOLLOW_GOALS_DISPLAY_NUMBER?>
        },
        pusher: {
            key: "<?=PUSHER_KEY?>"
        }
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

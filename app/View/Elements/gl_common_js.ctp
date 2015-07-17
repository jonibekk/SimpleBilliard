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
<?php //echo $this->Html->script('vendor/jquery-2.1.0.min');
echo $this->Html->script('vendor/jquery-1.11.1.min');
echo $this->Html->script('vendor/bootstrap.min');
echo $this->Html->script('vendor/jasny-bootstrap.min');
echo $this->Html->script('vendor/bootstrapValidator.min');
echo $this->Html->script('vendor/bootstrap-switch.min');
echo $this->Html->script('vendor/bvAddition');
echo $this->Html->script('vendor/pnotify.custom.min');
echo $this->Html->script('vendor/jquery.nailthumb.1.1.min');
echo $this->Html->script('vendor/jquery.autosize.min');
echo $this->Html->script('vendor/jquery.lazy.min');
echo $this->Html->script('vendor/lightbox.min');
echo $this->Html->script('vendor/jquery.showmore.min');
echo $this->Html->script('vendor/ZeroClipboard.min');
echo $this->Html->script('vendor/placeholders.min');
echo $this->Html->script('vendor/customRadioCheck.min');
echo $this->Html->script('vendor/select2.min');
echo $this->Html->script('vendor/bootstrap-datepicker.min');
echo $this->Html->script('vendor/locales/bootstrap-datepicker.ja');
echo $this->Html->script('vendor/moment.min');
echo $this->Html->script('vendor/pusher.min');
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
                d: "<?=__d('validate', "利用規約に同意してください。")?>",
                e: "<?=__d('validate', "数字、英小文字、英大文字の混在にしてください。")?>",
                f: "<?=__d('validate', "パスワードにメールアドレスと同一のものを指定する事はできません。")?>"
            },
            notice: {
                a: "<?=__d('gl',"入力が途中です。このまま移動しますか？")?>",
                b: "<?=__d('gl',"スペルを入力してください。")?>",
                c: "<?=__d('gl',"エラーが発生しました。データ取得できません。")?>",
                d: "<?=__d('gl',"エラーが発生しました。")?>",
                e: "<?=__d('validate',"開始日が期限を過ぎています。")?>",
                f: "<?=__d('validate',"期限が開始日以前になっています。")?>",
                g: "<?=__d('validate',"コメント送信できませんでした")?>",
                h: "<?=__d('gl',"このコメントは削除されました。")?>",
                i: "<?=__d('gl',"コメントを取得できませんでした。")?>"
            },
            info: {
                a: "<?=__d('gl',"クリップボードに投稿URLをコピーしました。")?>",
                b: "<?=__d('gl',"読込中･･･")?>",
                c: "<?=__d('gl',"検索中･･･")?>",
                d: "<?=__d('gl',"フォロー中")?>",
                e: "<?=__d('gl', "もっと見る") ?>",
                f: "<?=__d('gl', "さらに投稿を読み込む ▼") ?>",
                g: "<?=__d('gl','これ以上のコメントがありません。')?>",
                h: "<?=__d('gl',"閉じる")?>",
                z: "<?=__d('gl',"フォロー")?>" // ToDo - must rename this var
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
            j: "<?=__d('gl',"項目までしか選択できません")?>",
            k: "<?=__d('gl',"出したい成果を選択する(オプション)")?>",
            l: "<?=__d('gl',"出したい成果はありません")?>",
            select_notify_range: "<?=__d('gl',"通知先を追加(オプション)")?>",
            public: "<?=__d('gl',"公開")?>",
            secret: "<?=__d('gl',"秘密")?>",
            select_public_circle: "<?=__d('gl',"公開サークルかメンバーを指定しよう")?>",
            select_secret_circle: "<?=__d('gl',"秘密サークルを指定しよう")?>",
            share_change_disabled: "<?=__d('gl',"サークルページでは切り替えられません")?>"
        },
        url: {
            a: "<?=$this->Html->url(['controller'=>'users','action'=>'ajax_select2_get_users'])?>",
            b: "<?=$this->Html->url(['controller'=>'circles','action'=>'ajax_select2_init_circle_members'])?>/",
            c: "<?=$this->Html->url(['controller'=>'goals','action'=>'ajax_toggle_follow','goal_id'=>""])?>",
            d: "<?=$this->Html->url(['controller'=>'posts','action'=>'ajax_post_like','post_id'=>""])?>",
            e: "<?=$this->Html->url(['controller'=>'posts','action'=>'ajax_comment_like','comment_id'=>""])?>",
            f: "<?=$this->Html->url(['controller'=>'notifications','action'=>'ajax_get_new_notify_count'])?>",
            g: "<?=$this->Html->url(['controller'=>'notifications','action'=>'ajax_get_latest_notify_items'])?>",
            h: "<?=$this->Html->url(['controller'=>'teams','action'=>'ajax_get_term_start_end'])?>",
            i: "<?=$this->Html->url(['controller'=>'teams','action'=>'ajax_get_team_member'])?>/",
            j: "<?=$this->Html->url(['controller'=>'teams','action'=>'ajax_get_team_member_init'])?>/",
            k: "<?=$this->Html->url(['controller'=>'teams','action'=>'ajax_get_current_team_group_list'])?>/",
            l: "<?=$this->Html->url(['controller'=>'teams','action'=>'ajax_get_current_not_2fa_step_user_list'])?>/",
            m: "<?=$this->Html->url(['controller'=>'teams','action'=>'ajax_get_current_team_admin_list'])?>/",
            n: "<?=$this->Html->url(['controller'=>'teams','action'=>'ajax_get_group_member'])?>/",
            o: "<?=$this->Html->url(['controller'=>'teams','action'=>'ajax_set_current_team_active_flag'])?>/",
            p: "<?=$this->Html->url(['controller'=>'teams','action'=>'ajax_set_current_team_admin_user_flag'])?>/",
            q: "<?=$this->Html->url(['controller'=>'teams','action'=>'ajax_set_current_team_evaluation_flag'])?>/",
            r: "<?=$this->Html->url(['controller'=>'teams','action'=>'ajax_get_term_start_end_by_edit'])?>",
            t: "<?=$this->Html->url(['controller'=>'teams','action'=>'ajax_get_invite_member_list'])?>",
            u: "<?=$this->Html->url(['controller'=>'teams','action'=>'ajax_get_team_vision'])?>/",
            v: "<?=$this->Html->url(['controller'=>'teams','action'=>'ajax_set_team_vision_archive'])?>/",
            w: "<?=$this->Html->url(['controller'=>'teams','action'=>'ajax_delete_team_vision'])?>/",
            x: "<?=$this->Html->url(['controller'=>'teams','action'=>'ajax_team_admin_user_check'])?>/",
            y: "<?=$this->Html->url(['controller'=>'teams','action'=>'ajax_get_group_vision'])?>/",
            z: "<?=$this->Html->url(['controller'=>'teams','action'=>'ajax_set_group_vision_archive'])?>/",
            aa: "<?=$this->Html->url(['controller'=>'teams','action'=>'ajax_delete_group_vision'])?>/",
            ab: "<?=$this->Html->url(['controller'=>'teams','action'=>'ajax_get_login_user_group_id'])?>/",
            ac: "<?=$this->Html->url(['controller'=>'teams','action'=>'ajax_get_team_vision_detail'])?>/",
            ad: "<?=$this->Html->url(['controller'=>'teams','action'=>'ajax_get_group_vision_detail'])?>/",
            ae: "<?=$this->Html->url(['controller'=>'users','action'=>'ajax_get_user_detail'])?>/",
            select2_secret_circle: "<?=$this->Html->url(['controller'=>'users','action'=>'ajax_select2_get_secret_circles'])?>/",
            select2_circle_user: "<?=$this->Html->url(['controller'=>'users','action'=>'ajax_select2_get_circles_users'])?>",
            user_page: "<?= $this->Html->url(['controller' => 'users', 'action' => 'view_goals', 'user_id' => '']) ?>",
            goal_followers: "<?=$this->Html->url(['controller'=>'goals','action'=>'ajax_get_followers'])?>",
            goal_members: "<?= $this->Html->url(['controller' => 'goals', 'action' => 'ajax_get_members']) ?>"
        },
        data: {
            a: <?=isset($select2_default)?$select2_default:"[]"?>,
            b: function (element, callback) {
                var data = [];
                var selected_values = element.val().split(',');

                var current_circle_item = {};
                <?php if (isset($current_circle) && $current_circle) : ?>
                current_circle_item = {
                    <?php if ($current_circle['Circle']['team_all_flg']): ?>
                    id: "public",
                    <?php else: ?>
                    id: "circle_<?=$current_circle['Circle']['id']?>",
                    <?php endif ?>
                    text: "<?=h($current_circle['Circle']['name'])?>",
                    image: "<?=$this->Upload->uploadUrl($current_circle, 'Circle.photo', ['style' => 'small'])?>"
                };
                <?php endif ?>

                var team_all_circle_item = {};
                <?php if (isset($team_all_circle) && $team_all_circle): ?>
                team_all_circle_item = {
                    id: 'public',
                    text: "<?=h($team_all_circle['Circle']['name'])?>",
                    image: "<?=$this->Upload->uploadUrl($team_all_circle, 'Circle.photo', ['style' => 'small'])?>"
                };
                <?php endif;?>

                for (var i = 0; i < selected_values.length; i++) {
                    if (selected_values[i] == current_circle_item.id) {
                        data.push(current_circle_item);
                    }
                    else if (selected_values[i] == team_all_circle_item.id) {
                        data.push(team_all_circle_item);
                    }
                }
                callback(data);
            },
            c: <?=isset($my_channels_json)?$my_channels_json:"[]"?>,
            d: "<?=viaIsSet($feed_filter)?>",
            e: <?=MY_GOALS_DISPLAY_NUMBER?>,
            f: <?=MY_COLLABO_GOALS_DISPLAY_NUMBER?>,
            g: <?=MY_FOLLOW_GOALS_DISPLAY_NUMBER?>,
            h: "<?=viaIsSet($circle_id)?>",
            i: "<?=$this->Session->read('current_team_id')?>",
            j: "<?= isset($posts)?count($posts):null?>",
            k: <?=MY_PREVIOUS_GOALS_DISPLAY_NUMBER?>,
            l: function (element, callback) {
                var data = [
                    {
                        id: "coach",
                        text: "<?=__d('gl',"コーチ")?>",
                        icon: "fa fa-venus-double",
                        locked: true
                    },
                    {
                        id: "followers",
                        text: "<?=__d('gl',"フォロワー")?>",
                        icon: "fa fa-heart",
                        locked: true
                    },
                    {
                        id: "collaborators",
                        text: "<?=__d('gl',"コラボレータ")?>",
                        icon: "fa fa-child",
                        locked: true
                    }
                ];
                callback(data);
            },
            select2_secret_circle: function (element, callback) {
                var data = [];
                var selected_value = element.val();

                var current_circle_item = {};
                <?php if (isset($current_circle)) : ?>
                current_circle_item = {
                    id: "circle_<?=$current_circle['Circle']['id']?>",
                    text: "<?=h($current_circle['Circle']['name'])?>",
                    image: "<?=$this->Upload->uploadUrl($current_circle, 'Circle.photo', ['style' => 'small'])?>"
                };
                <?php endif ?>

                // 秘密サークルのフィードページの場合
                if (selected_value && selected_value == current_circle_item.id) {
                    current_circle_item.locked = true;
                    data.push(current_circle_item);
                }
                callback(data);
            }
        },
        pusher: {
            key: "<?=PUSHER_KEY?>",
            socket_id: ""
        },
        notify_auto_update_sec: <?=NOTIFY_AUTO_UPDATE_SEC?>,
        new_notify_cnt: <?=isset($new_notify_cnt)?$new_notify_cnt:0?>,
        common_form_type: "<?= isset($common_form_type)?$common_form_type:null?>"
    };


    <?php if(isset($mode_view)):?>
    <?php if($mode_view == MODE_VIEW_TUTORIAL):?>
    $("#modal_tutorial").modal('show');
    <?php endif;?>
    <?php endif;?>
</script>
<?= $this->Session->flash('click_event') ?>
<?php echo $this->Session->flash('pnotify');
//環境を識別できるようにリボンを表示
?>
<?php if (ENV_NAME == "stg"): ?>
    <p class="ribbon ribbon-staging">Staging</p>
<?php elseif (ENV_NAME == "hotfix"): ?>
    <p class="ribbon ribbon-hotfix">Hotfix</p>
<?php elseif (ENV_NAME == "local"): ?>
    <p class="ribbon ribbon-local">Local</p>
<?php endif; ?>
<!-- END app/View/Elements/gl_common_js.ctp -->

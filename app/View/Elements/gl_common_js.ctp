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
 * @var                    $is_mb_app
 * @var                    $notify_setting
 * @var                    $unread_msg_post_ids
 */
?>
<!-- START app/View/Elements/gl_common_js.ctp -->
<?php
echo $this->Html->script('vendors.min');
// echo $this->Html->script('vendor/jquery-2.1.0.min');
// echo $this->Html->script('vendor/bootstrap.min');
// echo $this->Html->script('vendor/jasny-bootstrap.min');
// echo $this->Html->script('vendor/bootstrapValidator.min');
// echo $this->Html->script('vendor/bootstrap-switch.min');
// echo $this->Html->script('vendor/bvAddition');
// echo $this->Html->script('vendor/pnotify.custom.min');
// echo $this->Html->script('vendor/jquery.nailthumb.1.1.min');
// echo $this->Html->script('vendor/jquery.autosize.min');
// echo $this->Html->script('vendor/jquery.lazy.min');
// echo $this->Html->script('vendor/lightbox-custom');
// echo $this->Html->script('vendor/jquery.showmore.min');
// echo $this->Html->script('vendor/placeholders.min');
// echo $this->Html->script('vendor/customRadioCheck.min');
// echo $this->Html->script('vendor/select2.min');
// echo $this->Html->script('vendor/bootstrap-datepicker.min');
// echo $this->Html->script('vendor/locales/bootstrap-datepicker.ja');
// echo $this->Html->script('vendor/moment.min');
// echo $this->Html->script('vendor/pusher.min');
// echo $this->Html->script('vendor/dropzone.js');
// echo $this->Html->script('vendor/jquery.flot.js');
// echo $this->Html->script('vendor/jquery.balanced-gallery');
// echo $this->Html->script('vendor/imagesloaded.pkgd.min');
// echo $this->Html->script('vendor/bootstrap.youtubepopup');
// echo $this->Html->script('vendor/require');
// echo $this->Html->script('vendor/exif');
// echo $this->Html->script('gl_basic');
echo $this->Html->script('goalous.min');
echo $this->Html->script('ng_app.min');
echo $this->Html->script('ng_controller');
echo $this->Html->script('vendor/angular/pusher-angular.min');
echo $this->Html->script('vendor/angular/ng-infinite-scroll.min');

?>
<!--suppress JSDuplicatedDeclaration -->
<script type="text/javascript">
    var cake = {
        message: {
            validate: {
                a: "<?=__('%1$d文字以上で%2$d文字以下で入力してください。',8,50)?>",
                b: "<?=__("パスワードが一致しません。")?>",
                c: "<?=__("10MB以下かつJPG、PNG、GIFのいずれかの形式を選択して下さい。")?>",
                d: "<?=__("利用規約に同意してください。")?>",
                e: "<?=__("数字、英小文字、英大文字の混在にしてください。")?>",
                f: "<?=__("パスワードにメールアドレスと同一のものを指定する事はできません。")?>",
                g: "<?=__("必須項目です。")?>",
                invalid_email: "<?= __("メールアドレスが正しくありません。") ?>",
                checking_email: "<?= __("メールアドレス確認中...") ?>",
                dropzone_file_too_big: "<?=__('アップロード出来るファイルサイズは{{maxFilesize}}MBまでです。')?>",
                dropzone_invalid_file_type: "<?=__('画像ファイルを選択してください。')?>",
                dropzone_max_files_exceeded: "<?=__('アップロード出来るファイル数は{{maxFiles}}個までです。')?>",
                dropzone_response_error: "<?=__('アップロードに失敗しました。')?>",
                dropzone_cancel_upload: "<?=__('アップロードをキャンセルしました。')?>",
                dropzone_cancel_upload_confirmation: "<?=__('アップロードをキャンセルしてよろしいですか？')?>",
                dropzone_uploading_not_end: "<?=__('全てのファイルのアップロードが完了していません。アップロード完了までお待ち下さい。')?>",
                dropzone_uploaded_file_expired: "<?=__('アップロードしたファイルの有効期限が切れています。再度アップロードしてください。')?>",
                date_format: "<?=__("日付はYYYY/MM/DDの形式で入力してください。")?>",
            },
            notice: {
                a: "<?=__("入力が途中です。このまま移動しますか？")?>",
                b: "<?=__("スペルを入力してください。")?>",
                c: "<?=__("エラーが発生しました。データ取得できません。")?>",
                d: "<?=__("エラーが発生しました。")?>",
                e: "<?=__("開始日が期限を過ぎています。")?>",
                f: "<?=__("期限が開始日以前になっています。")?>",
                g: "<?=__("コメント送信できませんでした")?>",
                h: "<?=__("このコメントは削除されました。")?>",
                i: "<?=__("コメントを取得できませんでした。")?>",
                search_result_zero: "<?=__("該当なし")?>",
                leave_secret_circle: "<?=__('秘密のサークルから脱退すると、自分の操作で再度参加できません。よろしいですか？')?>"
            },
            info: {
                a: "<?=__("クリップボードに投稿URLをコピーしました。")?>",
                b: "<?=__("読込中･･･")?>",
                c: "<?=__("検索中･･･")?>",
                d: "<?=__("フォロー中")?>",
                e: "<?=__("もっと見る") ?> ",
                f: "<?=__("さらに投稿を読み込む ▼") ?> ",
                g: "<?=__('これ以上のコメントがありません。')?>",
                h: "<?=__("閉じる")?>",
                z: "<?=__("フォロー")?>", // ToDo - must rename this var
                copy_url: "<?=__("以下のURLをコピーしてください。")?>"
            }
        },
        word: {
            a: "<?=__("自分のみ")?>",
            b: "<?=__("参加")?>",
            c: "<?=__("不参加")?>",
            d: "<?=__("該当なし")?>",
            e: "<?=__("入力してください。")?>",
            g: "<?=__("検索文字列が")?>",
            h: "<?=__("文字長すぎます")?>",
            i: "<?=__("最多で")?>",
            j: "<?=__("項目までしか選択できません")?>",
            k: "<?=__("達成要素を選択する(オプション)")?>",
            l: "<?=__("達成要素はありません")?>",
            select_notify_range: "<?=__("通知先を追加(オプション)")?>",
            public: "<?=__("公開")?>",
            secret: "<?=__("秘密")?>",
            select_public_circle: "<?=__("公開サークルかメンバーを指定しよう")?>",
            select_public_message: "<?=__("メンバーを指定しよう")?>",
            select_secret_circle: "<?=__("秘密サークルを指定しよう")?>",
            share_change_disabled: "<?=__("サークルページでは切り替えられません")?>",
            success: "<?=__("成功")?>",
            error: "<?=__("エラー")?>",
            cancel: "<?=__("キャンセル")?>",
            search_placeholder_user: "<?=__("名前を入力")?>",
            search_placeholder_goal: "<?=__("ゴール名を入力")?>",
            search_placeholder_circle: "<?=__("サークル名を入力")?>",
            leave_circle: "<?=__("サークルを脱退する")?>",
            config: "<?=__("設定")?>"
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
            af: "<?=$this->Html->url(['controller'=>'notifications','action'=>'ajax_get_new_message_notify_count'])?>",
            ag: "<?=$this->Html->url(['controller'=>'notifications','action'=>'ajax_get_latest_message_notify_items'])?>",
            an: "<?=$this->Html->url(['controller'=>'notifications','action'=>'ajax_mark_all_read'])?>",
            notifications: "<?=$this->Html->url(['controller'=>'notifications','action'=>'ajax_index'])?>",
            ah: "<?=$this->Html->url(['controller'=>'posts','action'=>'ajax_get_message'])?>/",
            ai: "<?=$this->Html->url(['controller'=>'posts','action'=>'ajax_put_message'])?>/",
            aj: "<?=$this->Html->url(['controller'=>'posts','action'=>'ajax_get_message_info'])?>/",
            ak: "<?=$this->Html->url(['controller'=>'posts','action'=>'ajax_put_message_read'])?>/",
            al: "<?=$this->Html->url(['controller'=>'posts','action'=>'ajax_get_message_list'])?>/",
            add_member_on_message: "<?=$this->Html->url(['controller'=>'users','action'=>'ajax_select_only_add_users'])?>",
            select2_secret_circle: "<?=$this->Html->url(['controller'=>'users','action'=>'ajax_select2_get_secret_circles'])?>/",
            select2_circle_user: "<?=$this->Html->url(['controller'=>'users','action'=>'ajax_select2_get_circles_users'])?>",
            select2_goals: "<?=$this->Html->url(['controller'=>'goals','action'=>'ajax_select2_goals'])?>",
            select2_circles: "<?=$this->Html->url(['controller'=>'circles','action'=>'ajax_select2_circles'])?>",
            user_page: "<?= $this->Html->url(['controller' => 'users', 'action' => 'view_goals', 'user_id' => '']) ?>",
            goal_page: "<?= $this->Html->url(['controller' => 'goals', 'action' => 'view_info', 'goal_id' => '']) ?>",
            circle_page: "<?= $this->Html->url(['controller' => 'posts', 'action' => 'feed', 'circle_id' => '']) ?>/",
            goal_followers: "<?=$this->Html->url(['controller'=>'goals','action'=>'ajax_get_followers'])?>",
            goal_members: "<?= $this->Html->url(['controller' => 'goals', 'action' => 'ajax_get_members']) ?>",
            goal_key_results: "<?= $this->Html->url(['controller' => 'goals', 'action' => 'ajax_get_key_results']) ?>",
            upload_file: "<?= $this->Html->url(['controller' => 'posts', 'action' => 'ajax_upload_file']) ?>",
            remove_file: "<?= $this->Html->url(['controller' => 'posts', 'action' => 'ajax_remove_file']) ?>",
            message_list: "<?= $this->Html->url(['controller' => 'posts', 'action' => 'message_list']) ?>",
            ajax_message_list: "<?= $this->Html->url(['controller' => 'posts', 'action' => 'ajax_message_list']) ?>",
            ajax_message: "<?= $this->Html->url(['controller' => 'posts', 'action' => 'ajax_message']) ?>",
            invite_member: "<?= $this->Html->url(['controller' => 'teams', 'action' => 'settings','#'=>'invite_member']) ?>",
            insight: "<?= $this->Html->url(['controller' => 'teams', 'action' => 'ajax_get_insight']) ?>",
            insight_circle: "<?= $this->Html->url(['controller' => 'teams', 'action' => 'ajax_get_insight_circle']) ?>",
            insight_ranking: "<?= $this->Html->url(['controller' => 'teams', 'action' => 'ajax_get_insight_ranking']) ?>",
            insight_graph: "<?= $this->Html->url(['controller' => 'teams', 'action' => 'ajax_get_insight_graph']) ?>",
            validate_email: "<?= $this->Html->url(['controller' => 'users', 'action' => 'ajax_validate_email']) ?>",
            join_circle: "<?= $this->Html->url(['controller' => 'circles', 'action' => 'ajax_join_circle']) ?>",
            ogp_info: "<?= $this->Html->url(['controller' => 'posts', 'action' => 'ajax_get_ogp_info']) ?>",
            regenerate_recovery_code: "<?= $this->Html->url(['controller' => 'users', 'action' => 'ajax_regenerate_recovery_code']) ?>",
            circle_setting: "<?= $this->Html->url(['controller' => 'circles', 'action' => 'ajax_change_setting']) ?>",
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
            csrf_token:<?= json_encode($this->Session->read('_Token'))?>,
            l: function (element, callback) {
                var data = [
                    {
                        id: "coach",
                        text: "<?=__("コーチ")?>",
                        icon: "fa fa-venus-double",
                        locked: true
                    },
                    {
                        id: "followers",
                        text: "<?=__("フォロワー")?>",
                        icon: "fa fa-heart",
                        locked: true
                    },
                    {
                        id: "collaborators",
                        text: "<?=__("コラボレータ")?>",
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
            },
            user_id: "<?= $this->Session->read('Auth.User.id')?>"
        },
        pusher: {
            key: "<?=PUSHER_KEY?>",
            socket_id: ""
        },
        notify_auto_update_sec: <?=NOTIFY_AUTO_UPDATE_SEC?>,
        new_notify_cnt: <?=isset($new_notify_cnt)?$new_notify_cnt:0?>,
        new_notify_message_cnt: <?=isset($new_notify_message_cnt)?$new_notify_message_cnt:0?>,
        common_form_type: "<?= isset($common_form_type)?$common_form_type:null?>",
        request_params: <?=json_encode($this->request->params)?>,
        is_mb_app: "<?= $is_mb_app ?>",
        is_mb_app_ios: "<?= $is_mb_app_ios ?>",
        pre_file_ttl: <?= PRE_FILE_TTL ?>,
        notify_setting: <?= isset($notify_setting)?json_encode($notify_setting):"''" ?>,
        unread_msg_post_ids: <?=isset($unread_msg_post_ids)?json_encode($unread_msg_post_ids):"''"?>,
        select2_query_limit: <?=SELECT2_QUERY_LIMIT?>
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

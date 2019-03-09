<?php
/**
 * Created by PhpStorm.
 * User: daikihirakata
 * Date: 7/21/16
 * Time: 00:40
 */
App::uses('AttachedFile', 'Model');
?>
<?= $this->App->viewStartComment() ?>
<script type="text/javascript">
    var cake = {
        env_name: "<?= ENV_NAME ?>",
        lang: "<?= Configure::read('Config.language') ?>",
        sentry_dsn: "<?= SENTRY_DSN ?>",
        stripe_publishable_key: "<?= STRIPE_PUBLISHABLE_KEY ?>",
        jwt_token: "<?= empty($jwt_token) ? "" : $jwt_token ?>",
        require_banner_notification: "<?=
            (isset($serviceUseStatus) && in_array($serviceUseStatus, [Team::SERVICE_USE_STATUS_FREE_TRIAL,Team::SERVICE_USE_STATUS_READ_ONLY]))
            || (isset($teamCreditCardStatus) && in_array($teamCreditCardStatus, [Team::STATUS_CREDIT_CARD_EXPIRED, Team::STATUS_CREDIT_CARD_EXPIRE_SOON]))
            || (isset($statusPaymentFailed) && $statusPaymentFailed)
            ?>",
        message: {
            validate: {
                a: "<?= __('%1$d or more and %2$d or less characters.', 8, 50)?>",
                b: "<?= __("Both of passwords are not same.")?>",
                c: "<?= __("10MB or less, and Please select one of the formats of JPG or PNG and GIF.")?>",
                d: "<?= __("Need to agree our terms.")?>",
                e: "<?= __('Please mix of numbers and alphanumeric.')?>",
                f: "<?= __("It is not possible to use the same as the email address as the password.")?>",
                g: "<?= __("Input is required.")?>",
                invalid_email: "<?= __("Email address is incorrect.") ?>",
                checking_email: "<?= __("Checking email address") ?>",
                dropzone_file_too_big: "<?= __('{{maxFilesize}}MB is the limit.')?>",
                dropzone_invalid_file_type: "<?= __('Only picture is allowed. (.jpg, .png, .gif)')?>",
                dropzone_max_files_exceeded: "<?= __('{{maxFiles}} is the limit you can upload once.')?>",
                dropzone_response_error: "<?=__('Failed to upload.')?>",
                dropzone_cancel_upload: "<?=__('Cancelled uploading.')?>",
                dropzone_cancel_upload_confirmation: "<?=__('Do you want to cancel uploading?')?>",
                dropzone_deleted: "<?=__('Deleted the file.')?>",
                dropzone_uploading_not_end: "<?=__('Not finished uploading yet, please wait just a moment.')?>",
                dropzone_uploaded_file_expired: "<?=__('Uploaded files are invalid due. Please try again.')?>",
                dropzone_error_allow_one_video: "<?=__('You can only post one video file.')?>",
                dropzone_video_cut_message: "<?=__('Videos that exceed %d seconds will automatically be trimmed.', 60)?>",
                date_format: "<?=__("Enter such date as YYYY/MM/DD.")?>",
                signup_team_name_length: "<?= __('%1$d or more and %2$d or less characters.', 1, 128)?>",
                signup_user_name_length: "<?= __('%1$d or more and %2$d or less characters.', 1, 128)?>",
                signup_user_name_alpha: "<?= __("It includes restricted strings. Allowed characters are only alphanumeric, space and apostrophe.") ?>",
                signup_privacy_policy_required: "<?= __("Selection required.") ?>",
                signup_birth_day_required: "<?= __("Selection required.") ?>",
                signup_term_required: "<?= __("Selection required.") ?>",
                signup_start_month_required: "<?= __("Selection required.") ?>",
                signup_password_min_length: "<?= __('At least %2$d characters is required.', 8, 8)?>",
                signup_password_max_length: "<?= __('%1$d or more and %2$d or less characters.', 8, 50)?>",
                signup_password_alpha_num_required: "<?= __("Please mix of numbers and alphanumeric.") ?>"
            },
            notice: {
                a: "<?=__("You haven't finished your input data yet. Do you want to leave without finishing?")?>",
                b: "<?=__("Please enter text.")?>",
                c: "<?=__("An error has occurred. Data can not be retrieved.")?>",
                d: "<?=__("An error has occurred.")?>",
                e: "<?=__("Start date has expired.")?>",
                f: "<?=__("The limit date must be after start date.")?>",
                g: "<?=__("Failed to comment.")?>",
                h: "<?=__("This comment was deleted.")?>",
                i: "<?=__("Failed to get comment.")?>",
                search_result_zero: "<?=__("N/A")?>",
                leave_secret_circle: "<?=__('After leaving secret circle, you can\'t join again by yourself. Do you really want to leave this secret circle?')?>",
                confirm_cancel_post: "<?=__('Do you really want to cancel this post?')?>",
                confirm_evaluation_start: "<?=__('Unable to cancel. Do you really want to start evaluations?')?>",
            },
            info: {
                a: "<?=__("Copied the post URL to the clipboard.")?>",
                b: "<?=__("Loading...")?>",
                c: "<?=__("Searching...")?>",
                d: "<?=__("Following")?>",
                e: "<?=__("More ") ?> ",
                f: "<?=__("View previous posts ▼") ?> ",
                g: "<?=__('No further comment.')?>",
                h: "<?=__("Close")?>",
                z: "<?=__("Follow")?>", // ToDo - must rename this var
                copy_url: "<?=__("Copy the URL")?>"
            }
        },
        word: {
            a: "<?=__("Only you")?>",
            b: "<?=__("Join")?>",
            c: "<?=__("...")?>",
            d: "<?=__("N/A")?>",
            e: "<?=__("Enter text.")?>",
            g: "<?=__("Search string")?>",
            h: "<?=__("is too long.")?>",
            i: "<?=__("At most,")?>",
            j: "<?=__("item can only be selected.")?>",
            k: "<?=__("Select a Key Result (optional)")?>",
            l: "<?=__("No Key Result")?>",
            select_notify_range: "<?=__("Add notified parties (optional)")?>",
            public: "<?=__("Public")?>",
            secret: "<?=__("Secret")?>",
            select_public_circle: "<?=__("Choose public circle(s) or member(s)")?>",
            select_public_message: "<?=__("Choose member(s)")?>",
            select_secret_circle: "<?=__("Choose secret circle(s)")?>",
            share_change_disabled: "<?=__("You can't switch in the circle page.")?>",
            members: "<?= __("Members")?>",
            goals: "<?= __("Goal")?>",
            circles: "<?= __("Circles")?>",
            end_search: "<?= __("There is no more data.")?>",
            no_results: "<?= __("No results found")?>",
            success: "<?=__("Success")?>",
            error: "<?=__("Error")?>",
            cancel: "<?=__("Cancel")?>",
            delete: "<?=__("Delete")?>",
            leave_circle: "<?=__("Leave circle.")?>",
            config: "<?=__("Settings")?>",
            waiting_message: "<?=__("Ironing your data. Please wait...")?>",
            remaining: "<?= __("%d remaining") ?>",
            view_all: "<?= __("View All") ?>",
            close: "<?= __("Close") ?>",
        },
        url: {
            a: "<?=$this->Html->url(['controller' => 'users', 'action' => 'ajax_select2_get_users'])?>",
            b: "<?=$this->Html->url(['controller' => 'circles', 'action' => 'ajax_select2_init_circle_members'])?>/",
            c: "<?=$this->Html->url(['controller' => 'goals', 'action' => 'ajax_toggle_follow', 'goal_id' => ""])?>",
            d: "<?=$this->Html->url(['controller' => 'posts', 'action' => 'ajax_post_like', 'post_id' => ""])?>",
            e: "<?=$this->Html->url(['controller' => 'posts', 'action' => 'ajax_comment_like', 'comment_id' => ""])?>",
            f: "<?=$this->Html->url(['controller' => 'notifications', 'action' => 'ajax_get_new_notify_count'])?>",
            g: "<?=$this->Html->url(['controller' => 'notifications', 'action' => 'ajax_get_latest_notify_items'])?>",
            h: "<?=$this->Html->url(['controller' => 'teams', 'action' => 'ajax_get_term_start_end'])?>",
            i: "<?=$this->Html->url(['controller' => 'teams', 'action' => 'ajax_get_team_member'])?>/",
            j: "<?=$this->Html->url(['controller' => 'teams', 'action' => 'ajax_get_team_member_init'])?>/",
            k: "<?=$this->Html->url(['controller' => 'teams', 'action' => 'ajax_get_current_team_group_list'])?>/",
            l: "<?=$this->Html->url([
                'controller' => 'teams',
                'action'     => 'ajax_get_current_not_2fa_step_user_list'
            ])?>/",
            m: "<?=$this->Html->url(['controller' => 'teams', 'action' => 'ajax_get_current_team_admin_list'])?>/",
            n: "<?=$this->Html->url(['controller' => 'teams', 'action' => 'ajax_get_group_member'])?>/",
            p: "<?=$this->Html->url(['controller' => 'teams', 'action' => 'ajax_set_current_team_admin_user_flag'])?>/",
            q: "<?=$this->Html->url(['controller' => 'teams', 'action' => 'ajax_set_current_team_evaluation_flag'])?>/",
            r: "<?=$this->Html->url(['controller' => 'teams', 'action' => 'ajax_get_term_start_end_by_edit'])?>",
            t: "<?=$this->Html->url(['controller' => 'teams', 'action' => 'ajax_get_invite_member_list'])?>",
            u: "<?=$this->Html->url(['controller' => 'teams', 'action' => 'ajax_get_team_vision'])?>/",
            v: "<?=$this->Html->url(['controller' => 'teams', 'action' => 'ajax_set_team_vision_archive'])?>/",
            w: "<?=$this->Html->url(['controller' => 'teams', 'action' => 'ajax_delete_team_vision'])?>/",
            x: "<?=$this->Html->url(['controller' => 'teams', 'action' => 'ajax_team_admin_user_check'])?>/",
            y: "<?=$this->Html->url(['controller' => 'teams', 'action' => 'ajax_get_group_vision'])?>/",
            z: "<?=$this->Html->url(['controller' => 'teams', 'action' => 'ajax_set_group_vision_archive'])?>/",
            aa: "<?=$this->Html->url(['controller' => 'teams', 'action' => 'ajax_delete_group_vision'])?>/",
            ab: "<?=$this->Html->url(['controller' => 'teams', 'action' => 'ajax_get_login_user_group_id'])?>/",
            ac: "<?=$this->Html->url(['controller' => 'teams', 'action' => 'ajax_get_team_vision_detail'])?>/",
            ad: "<?=$this->Html->url(['controller' => 'teams', 'action' => 'ajax_get_group_vision_detail'])?>/",
            ae: "<?=$this->Html->url(['controller' => 'users', 'action' => 'ajax_get_user_detail'])?>/",
            af: "<?=$this->Html->url([
                'controller' => 'notifications',
                'action'     => 'ajax_get_new_message_notify_count'
            ])?>",
            ag: "<?=$this->Html->url([
                'controller' => 'notifications',
                'action'     => 'ajax_get_latest_message_notify_items'
            ])?>",
            an: "<?=$this->Html->url(['controller' => 'notifications', 'action' => 'ajax_mark_all_read'])?>",
            notifications: "<?=$this->Html->url(['controller' => 'notifications', 'action' => 'ajax_index'])?>",
            am: "/api/v1/invitations/reInvite",
            add_member_on_message: "<?=$this->Html->url([
                'controller' => 'users',
                'action'     => 'ajax_select_add_members_on_message'
            ])?>",
            select2_secret_circle: "<?=$this->Html->url([
                'controller' => 'users',
                'action'     => 'ajax_select2_get_secret_circles'
            ])?>/",
            select2_circle_user: "<?=$this->Html->url([
                'controller' => 'users',
                'action'     => 'ajax_select2_get_circles_users'
            ])?>",
            select_search: "<?=$this->Html->url(['controller' => 'searchs', 'action' => 'ajax_get_search_results'])?>",
            select2_goals: "<?=$this->Html->url(['controller' => 'goals', 'action' => 'ajax_select2_goals'])?>",
            select2_circles: "<?=$this->Html->url(['controller' => 'circles', 'action' => 'ajax_select2_circles'])?>",
            user_page: "<?= $this->Html->url(['controller' => 'users', 'action' => 'view_goals', 'user_id' => '']) ?>",
            goal_page: "<?= $this->Html->url(['controller' => 'goals', 'action' => 'view_krs', 'goal_id' => '']) ?>",
            circle_page: "<?= $this->Html->url(['controller' => 'posts', 'action' => 'feed', 'circle_id' => '']) ?>/",
            goal_followers: "<?=$this->Html->url(['controller' => 'goals', 'action' => 'ajax_get_followers'])?>",
            goal_members: "<?= $this->Html->url(['controller' => 'goals', 'action' => 'ajax_get_members']) ?>",
            goal_key_results: "<?= $this->Html->url(['controller' => 'goals', 'action' => 'ajax_get_key_results']) ?>",
            upload_file: "/api/v1/files/upload",
            remove_file: "<?= $this->Html->url(['controller' => 'posts', 'action' => 'ajax_remove_file']) ?>",
            message_list: "<?= $this->Html->url(['controller' => 'topics','action'=>'index']) ?>",
            ajax_message_list: "<?= $this->Html->url(['controller' => 'posts', 'action' => 'ajax_message_list']) ?>",
            ajax_message: "<?= $this->Html->url(['controller' => 'posts', 'action' => 'ajax_message']) ?>",
            invite_member: "<?= $this->Html->url([
                'controller' => 'users',
                'action'     => 'invite',
            ]) ?>",
            insight: "<?= $this->Html->url(['controller' => 'teams', 'action' => 'ajax_get_insight']) ?>",
            insight_circle: "<?= $this->Html->url(['controller' => 'teams', 'action' => 'ajax_get_insight_circle']) ?>",
            insight_ranking: "<?= $this->Html->url([
                'controller' => 'teams',
                'action'     => 'ajax_get_insight_ranking'
            ]) ?>",
            insight_graph: "<?= $this->Html->url(['controller' => 'teams', 'action' => 'ajax_get_insight_graph']) ?>",
            validate_email: "<?= $this->Html->url(['controller' => 'users', 'action' => 'ajax_validate_email']) ?>",
            join_circle: "<?= $this->Html->url(['controller' => 'circles', 'action' => 'ajax_join_circle']) ?>",
            ogp_info: "<?= $this->Html->url(['controller' => 'posts', 'action' => 'ajax_get_ogp_info']) ?>",
            regenerate_recovery_code: "<?= $this->Html->url([
                'controller' => 'users',
                'action'     => 'ajax_regenerate_recovery_code'
            ]) ?>",
            circle_setting: "<?= $this->Html->url(['controller' => 'circles', 'action' => 'ajax_change_setting']) ?>",
            signup_ajax_validate_email: "<?= $this->Html->url([
                'controller' => 'signup',
                'action'     => 'ajax_validate_email'
            ]) ?>",
            team_ajax_validate_email_can_invite: "<?= $this->Html->url([
                'controller' => 'teams',
                'action'     => 'ajax_validate_email_can_invite'
            ]) ?>",
            inactivate_team_member: "<?=$this->Html->url(['controller' => 'teams', 'action' => 'ajax_inactivate_team_member'])?>/",
            activate_team_member: "<?=$this->Html->url(['controller' => 'teams', 'action' => 'activate_team_member'])?>/",
            route_url: "<?= Router::fullbaseUrl() ?>"
        },
        data: {
            a: <?=isset($select2_default) ? $select2_default : "[]"?>,
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
            c: <?=isset($my_channels_json) ? $my_channels_json : "[]"?>,
            d: "<?=viaIsSet($feed_filter)?>",
            e: <?=MY_GOALS_DISPLAY_NUMBER?>,
            f: <?=MY_COLLABO_GOALS_DISPLAY_NUMBER?>,
            g: <?=MY_FOLLOW_GOALS_DISPLAY_NUMBER?>,
            h: "<?=viaIsSet($circle_id)?>",
            j: "<?= isset($posts) ? count($posts) : null?>",
            k: <?=MY_PREVIOUS_GOALS_DISPLAY_NUMBER?>,
            csrf_token:<?= json_encode($this->Session->read('_Token'))?>,
            l: function (element, callback) {
                var data = [
                    {
                        id: "coach",
                        text: "<?=__("Coach")?>",
                        icon: "fa fa-venus-double",
                        locked: true
                    },
                    {
                        id: "followers",
                        text: "<?=__("Follower")?>",
                        icon: "fa fa-heart",
                        locked: true
                    },
                    {
                        id: "collaborators",
                        text: "<?=__("Collaborator")?>",
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
            team_id: "<?=$this->Session->read('current_team_id')?>",
            user_id: "<?= $this->Session->read('Auth.User.id')?>",
            kr_value_unit_list: <?= json_encode(KeyResult::$UNIT)?>,
            google_tag_manager_id: "<?= GOOGLE_TAG_MANAGER_ID ?>",
            timezones: <?= isset($timezones) ? json_encode($timezones) : "''" ?>,
            // Array with country codes
            countryCodes: <?= json_encode(array_map(function($tag) { return $tag['code']; }, Configure::read("countries"))); ?>,
            is_edit_mode: <?= (isset($common_form_mode) && $common_form_mode == 'edit') ? 'true' : 'false' ?>

        },
        pusher: {
            key: "<?=PUSHER_KEY?>",
            socket_id: ""
        },
        translation: {
            // Setup guide
            "Failed to add an action.": "<?= __("Failed to add an action.") ?>",
            "Failed to post.": "<?= __("Failed to post.")?>",
            "Do an action": "<?= __("Do an action") ?>",
            "Action": "<?= __("Action") ?>",
            'Upload an image as your action': "<?= __('Upload an image as your action') ?>",
            "Write an action...": "<?= __("Write an action...") ?>",
            "Create a goal": "<?= __("Create a goal") ?>",
            'Create another goal': "<?= __('Create another goal') ?>",
            'Back': "<?= __('Back') ?>",
            "Set up Goalous": "<?= __("Set up Goalous") ?>",
            "Do an action": "<?= __("Do an action") ?>",
            "Next": "<?= __("Next") ?>",
            'Login from mobile app': "<?= __('Login from mobile app') ?>",
            'Install Android app': "<?= __('Install Android app') ?>",
            'Requires Android 6.0 or later.': "<?= __('Requires Android 6.0 or later.') ?>",
            'Install iOS app': "<?= __('Install iOS app') ?>",
            'Requires iOS 8.4 or later.': "<?= __('Requires iOS 8.4 or later.') ?>",
            "If you don't have a mobile device.": "<?= __("If you don't have a mobile device.") ?>",
            "Join a circle": "<?= __("Join a circle") ?>",
            "Circle name": "<?= __("Circle name") ?>",
            "Members": "<?= __("Members") ?>",
            "Administrators": "<?= __("Administrators") ?>",
            "Public": "<?= __("Public") ?>",
            "Anyone can see the circle, its members and their posts.": "<?= __("Anyone can see the circle, its members and their posts.") ?>",
            "Privacy": "<?= __("Privacy") ?>",
            "Only members can find the circle and see posts.": "<?= __("Only members can find the circle and see posts.") ?>",
            "Circle Description": "<?= __("Circle Description") ?>",
            "Circle Image": "<?= __("Circle Image") ?>",
            "Select an image": "<?= __("Select an image") ?>",
            "Reselect an image": "<?= __("Reselect an image") ?>",
            "Create": "<?= __("Create ") ?>",
            "Create a goal": "<?= __("Create a goal") ?>",
            "Purpose": "<?= __("Purpose") ?>",
            "Goal Name": "<?= __("Goal Name") ?>",
            'Unit': "<?= __('Unit') ?>",
            "Initial point": "<?= __("Initial point") ?>",
            "Achieve point": "<?= __("Achieve point") ?>",
            "Due Date": "<?= __("Due Date") ?>",
            "Goal Image": "<?= __("Goal Image") ?>",
            "Select an image": "<?= __("Select an image") ?>",
            "Talk with team members": "<?= __("Talk with team members") ?>",
            "Lunch with team members": "<?= __("Lunch with team members") ?>",
            "Hear complaints of team members": "<?= __("Hear complaints of team members") ?>",
            'Create your own': "<?= __('Create your own') ?>",
            "Do something with team members": "<?= __("Do something with team members") ?>",
            "You do something worthwhile.": "<?= __("You do something worthwhile.") ?>",
            "Open yourself": "<?= __("Open yourself") ?>",
            "Increasing people who know you.": "<?= __("Increasing people who know you.") ?>",
            "Give something to team members": "<?= __("Give something to team members") ?>",
            "Be happy everyone.": "<?= __("Be happy everyone.") ?>",
            'Post to a circle': "<?= __('Post to a circle') ?>",
            'Create another circle': "<?= __('Create another circle') ?>",
            'Post to a circle': "<?= __('Post to a circle') ?>",
            "Write something...": "<?= __("Write something...") ?>",
            "Input your profile": "<?= __("Input your profile") ?>",
            "Your profile picture": "<?= __("Your profile picture") ?>",
            "Your self-info": "<?= __("Your self-info") ?>",
            "Submit": "<?= __("Submit") ?>",
            'Set your profile picture and self-info.': "<?= __('Set your profile picture and self-info.') ?>",
            'Login from mobile app': "<?= __('Login from mobile app') ?>",
            "Install Goalous's iOS and Android apps.": "<?= __("Install Goalous's iOS and Android apps.") ?>",
            'Create a goal': "<?= __('Create a goal') ?>",
            'Create or collaborate with a goal.': "<?= __('Create or collaborate with a goal.') ?>",
            'Do an action': "<?= __('Do an action') ?>",
            'Add an Action for your Goal.': "<?= __('Add an Action for your Goal.') ?>",
            'Join a circle': "<?= __('Join a circle') ?>",
            'Create a circle or join.': "<?= __('Create a circle or join.') ?>",
            'Post to a circle': "<?= __('Post to a circle') ?>",
            'Share your topic with a circle.': "<?= __('Share your topic with a circle.') ?>",
            'STEPS LEFT': "<?= __('STEPS LEFT') ?>",
            'Create a circle': "<?= __('Create a circle') ?>",
            'Completed': "<?= __('Completed') ?>",
            'Excellent!': "<?= __('Excellent!') ?>",
            'I have no iOS/Android devices': "<?= __('I have no iOS/Android devices') ?>",
            'Make a good Goal to discuss with your project members.': "<?= __('Make a good Goal to discuss with your project members.') ?>",
            'Have a common Goal': "<?= __('Have a common Goal') ?>",
            'Action for your Goal': "<?= __('Action for your Goal') ?>",
            "Let's action to show your activity.": "<?= __("Let's action to show your activity.") ?>",
            "Improve your orgainization": "<?= __("Improve your orgainization") ?>",
            "Please choose one.": "<?= __("Please choose one.") ?>",
            "Writing working columns": "<?= __("Writing working columns") ?>",
            "Sharing your lovely foods": "<?= __("Sharing your lovely foods") ?>",
            "Writing your insistence": "<?= __("Writing your insistence") ?>",
            "Embodying the orgainization motto": "<?= __("Embodying the orgainization motto") ?>",
            "Prasing someone": "<?= __("Prasing someone") ?>",
            "Including your orgainization improvements": "<?= __("Including your orgainization improvements") ?>",
            "Team has been changed, press ok to reload!": "<?=__("Team has been changed, press ok to reload!")?>",
            "Post": "<?= __("Post") ?>",

            // Signup
            "Check your email!": "<?= __("Check your email!") ?>",
            "We've sent a six-digit confirmation code to %s. Enter it below to confirm your email address.": "<?= __("We've sent a six-digit confirmation code to %s. Enter it below to confirm your email address.",
                $signup_inputed_email = isset($signup_inputed_email) ? $signup_inputed_email : '')?>",
            "What's your name?": "<?= __("What's your name?") ?>",
            "Your name will only be displayed to your team on Goalous.": "<?= __("Your name will only be displayed to your team on Goalous.") ?>",
            "I want to receive news and updates by email from Goalous.": "<?= __("I want to receive news and updates by email from Goalous.") ?>",
            "Set your password": "<?= __("Set your password") ?>",
            "Create your password to login to Goalous.": "<?= __("Create your password to login to Goalous.") ?>",
            "Please don't close this window.": "<?= __("Please don't close this window.") ?>",
            "What do you want to call your Goalous team?": "<?= __("What do you want to call your Goalous team?") ?>",
            "Create a name for your team. A team is a group that can share goals, actions and posts with each other. People outside of the team can't access this information.": "<?= __("Create a name for your team. A team is a group that can share goals, actions and posts with each other. People outside of the team can't access this information.") ?>",
            "Use 8 or more characters including at least one number.": "<?= __("Use 8 or more characters including at least one number.") ?>",
            "Choose your team's (company's) term": "<?=__("Choose your team's (company's) term") ?>",
            "Your name": "<?= __("Your name") ?>",
            "Birthday": "<?= __("Birthday(Optional)") ?>",
            "I agree to %s and %s of Goalous.": '<?= __("I agree to %s and %s of Goalous.",
                '<a href="/terms" target="_blank" class="signup-privacy-policy-link">' . __("Terms of Service") . '</a>',
                '<a href="/privacy_policy" target="_blank" class="signup-privacy-policy-link">' . __("Privacy Policy") . '</a>') ?>',
            "first name (eg. John)": "<?= __("first name (eg. John)") ?>",
            "last name (eg. Smith)": "<?= __("last name (eg. Smith)") ?>",
            "eg. Team Goalous": "<?= __("eg. Team Goalous") ?>",
            "Team Name": "<?= __("Team Name") ?>",
            "Term": "<?= __("Term") ?>",
            "Timezone": "<?= __("Timezone") ?>",
            "Please select": "<?= __("Please select") ?>",
            "Change": "<?= __("Change") ?>",
            "Quater": "<?= __("Quater") ?>",
            "Half a year": "<?= __("Half a year") ?>",
            "Year": "<?= __("Year") ?>",
            "Password": "<?= __("Password") ?>",
            "Next": "<?= __("Next") ?>",
            "Jan": "<?= __("Jan") ?>",
            "Feb": "<?= __("Feb") ?>",
            "Mar": "<?= __("Mar") ?>",
            "Apr": "<?= __("Apr") ?>",
            "May": "<?= __("May") ?>",
            "Jun": "<?= __("Jun") ?>",
            "Jul": "<?= __("Jul") ?>",
            "Aug": "<?= __("Aug") ?>",
            "Sep": "<?= __("Sep") ?>",
            "Oct": "<?= __("Oct") ?>",
            "Nov": "<?= __("Nov") ?>",
            "Dec": "<?= __("Dec") ?>",
            "I receive the news and updates by email from Goalous.": "<?= __("I receive the news and updates by email from Goalous.") ?>",
            "Choose your team name.": "<?= __("Choose your team name.") ?>",
            "Select your present term": "<?= __("Select your present term") ?>",
            "Password is incorrect.": "<?= __("Password is incorrect.") ?>",
            "Select your next term": "<?= __("Select your next term") ?>",
            "3 months": "<?= __("3 months") ?>",
            "6 months": "<?= __("6 months") ?>",
            "12 months": "<?= __("12 months") ?>",
            "The default length of any future terms are automatically set to 3 months.": "<?= __("The default length of any future terms are automatically set to 3 months.") ?>",
            "The default length of any future terms are automatically set to 6 months.": "<?= __("The default length of any future terms are automatically set to 6 months.") ?>",
            "The default length of any future terms are automatically set to 12 months.": "<?= __("The default length of any future terms are automatically set to 12 months.") ?>",
            "You can change this setting at any time.": "<?= __("You can change this setting at any time.") ?>",
            "The term can be based on your corporate / financial calendar, personal evaluations or any period of time the works best for your company.Choose the months that start and end your first two terms.": "<?= __("The term can be based on your corporate / financial calendar, personal evaluations or any period of time the works best for your company.Choose the months that start and end your first two terms.") ?>",
            //Creating a Goal
            "": "<?=__("")?>",
            "What is your goal ?": "<?=__("What is your goal ?")?>",
            "Imagine an ambitious outcome that you want to achieve. If your organization has a vision, you should follow it.": "<?=__("Imagine an ambitious outcome that you want to achieve. If your organization has a vision, you should follow it.")?>",
            "Goal name": "<?=__("Goal name")?>",
            "eg. Spread Goalous users in the world": "<?=__("eg. Spread Goalous users in the world")?>",
            "View samples": "<?=__("View samples")?>",
            "See Other": "<?=__("See Other")?>",
            "Next →": "<?=__("Next →")?>",
            "Cancel": "<?=__("Cancel")?>",
            "Vision": "<?=__("Vision")?>",
            "Set labels": "<?=__("Set labels")?>",
            "To make it easier to find your goal, let's set labels. And if your organization has goal categories, you can select them here.": "<?=__("To make it easier to find your goal, let's set labels. And if your organization has goal categories, you can select them here.")?>",
            "Category": "<?=__("Category")?>",
            "Labels": "<?=__("Labels")?>",
            "Set goal details": "<?=__("Set goal details")?>",
            "Customize your goal using the below options.": "<?=__("Customize your goal using the below options.")?>",
            "Goal image": "<?=__("Goal image")?>",
            "Upload an image": "<?=__("Upload an image")?>",
            "This Term": "<?=__("This Term")?>",
            "Next Term": "<?=__("Next Term")?>",
            "Do you want to evaluate this Goal ?": "<?=__("Do you want to evaluate this Goal ?")?>",
            "View more options": "<?=__("View more options")?>",
            "Description": "<?=__("Description")?>",
            "End date": "<?=__("End date")?>",
            "Weight": "<?=__("Weight")?>",
            "Set Top Key Result": "<?=__("Set Top Key Result")?>",
            "Create a clear and most important Key Result for your goal.": "<?=__("Create a clear and most important Key Result for your goal.")?>",
            "Top Key Result": "<?=__("Top Key Result")?>",
            "eg. Increase Goalous weekly active users": "<?=__("eg. Increase Goalous weekly active users")?>",
            "Add description": "<?=__("Add description")?>",
            "Save and share": "<?=__("Save and share")?>",
            //goal approval
            "Set as a target for evaluation?": "<?=__("Set as a target for evaluation?")?>",
            "Leader": "<?=__("Leader")?>",
            "Do you think this Top Key Result is clear ?": "<?=__("Do you think this Top Key Result is clear ?")?>",
            "Yes": "<?=__("Yes")?>",
            "No": "<?=__("No")?>",
            "Do you think this Top Key Result is the most important to achieve the goal?": "<?=__("Do you think this Top Key Result is the most important to achieve the goal?")?>",
            "Add as a target for evaluation ?": "<?=__("Add as a target for evaluation ?")?>",
            "Add your comment (optional)": "<?=__("Add your comment (optional)")?>",
            "View Previous": "<?=__("View Previous")?>",
            "View all %s comments": "<?=__("View all %s comments")?>",
            "more": "<?=__("more")?>",
            "Collaborator": "<?=__("Collaborator")?>",
            "This Top Key Result is clear and most important.": "<?=__("This Top Key Result is clear and most important.")?>",
            "This Top Key Result is not most important.": "<?=__("This Top Key Result is not most important.")?>",
            "This Top Key Result is not clear.": "<?=__("This Top Key Result is not clear.")?>",
            "Edit Goal": "<?=__("Edit Goal")?>",
            "Confirm": "<?=__("Confirm")?>",
            "Skip": "<?=__("Skip")?>",
            "Save & Reapply": "<?=__("Save & Reapply")?>",
            "Edit Role": "<?=__("Edit Role")?>",
            "Withdraw": "<?=__("Withdraw")?>",
            "Goal approval list": "<?=__("Goal approval list")?>",
            "New": "<?=__("New")?>",
            "Reapply": "<?=__("Reapply")?>",
            "Evaluated": "<?=__("Evaluated")?>",
            "Not Evaluated": "<?=__("Not Evaluated")?>",
            "Waiting": "<?=__("Waiting")?>",
            "Waiting for approval": "<?=__("Waiting for approval")?>",
            "Goal details": "<?= __("Goal details") ?>",
            "Add by pressing the Enter.(You can save maximum 5 labels)": "<?= __("Add by pressing the Enter.(You can save maximum 5 labels)") ?>",
            "Withdrawn": "<?=__("Withdrawn")?>",
            "Evaluation target goals are listed up here.": "<?= __("Evaluation target goals are listed up here.") ?>",
            "There are no evaluation target goals.": "<?= __("There are no evaluation target goals.") ?>",
            "Previous": "<?= __("Previous") ?>",
            "Send": "<?= __("Send") ?>",
            "Role": "<?= __("Role") ?>",
            // goal edit
            "Confirm this Goal": "<?= __("Confirm this Goal") ?>",
            "Edit goal & Top Key Result": "<?=  __("Edit goal & Top Key Result") ?>",
            "Save changes": "<?= __("Save changes") ?>",
            "Optional": "<?= __("Optional") ?>",
            "Top Key Result name": "<?= __("Top Key Result name") ?>",
            "Measurement type": "<?= __("Measurement type") ?>",
            "Creation Date": "<?= __("Creation Date") ?>",
            "Actions number": "<?= __("Actions number") ?>",
            "Key results number": "<?= __("Key results number") ?>",
            "Followers number": "<?= __("Followers number") ?>",
            "Collaborators number": "<?= __("Collaborators number") ?>",
            "Progress rate": "<?= __("Progress rate") ?>",
            "Follow": "<?= __("Follow") ?>",
            "Following": "<?= __("Following") ?>",
            "Search by goal name": "<?= __("Search by goal name") ?>",
            "Filter": "<?= __("Filter") ?>",
            "Close": "<?=__("Close")?>",
            "Search result": "<?=__("Search result")?>",
            " count": "<?=__(" count")?>",
            "Add by pressing the Enter.(You can search maximum 3 labels)": "<?= __("Add by pressing the Enter.(You can search maximum 3 labels)") ?>",
            'Current Term': "<?=__("Current Term")?>",
            "Next Term": "<?=__("Next Term")?>",
            "Previous Term": "<?=__("Previous Term")?>",
            "More Previous": "<?=__("More Previous")?>",
            "All": "<?=__("All")?>",
            "Complete": "<?=__("Complete")?>",
            "Incomplete": "<?=__("Incomplete")?>",
            "Progress": "<?=__("Progress")?>",
            "Request goal approval": "<?=__("Request goal approval")?>",
            "Name": "<?=__("Name")?>",
            "Level of achievement": "<?=__("Level of achievement")?>",
            "Achieved": "<?= __("Achieved") ?>",
            "Unachieved": "<?= __("Unachieved") ?>",
            "Would you like to save?": "<?= __("Would you like to save?") ?>",
            "If you change the unit, all progress of KR will be reset.": "<?= __("If you change the unit, all progress of KR will be reset.") ?>",
            "Current": "<?= __("Current") ?>",
            "All progress of this KR will be reset, is it really OK?": "<?= __("All progress of this KR will be reset, is it really OK?") ?>",
            "Updated KR.": "<?= __("Updated KR.") ?>",
            "If you change the goal term from the next term to this term, the goal and all KRs’ deadlines will be changed to the last day of the current term.": "<?= __("If you change the goal term from the next term to this term, the goal and all KRs’ deadlines will be changed to the last day of the current term.") ?>",
            // kr column
            "All Goals": "<?= __("All Goals") ?>",
            "PROGRESS": "<?= __("PROGRESS") ?>",
            "All goal's total you have.": "<?= __("All goal's total you have.") ?>",
            "Sweet Spot(Drive for it!)": "<?= __("Sweet Spot(Drive for it!)") ?>",
            "Add member(s)": "<?= __("Add member(s)") ?>",
            "Set topic name": "<?= __("Set topic name") ?>",
            "Leave me": "<?= __("Leave me") ?>",
            "Reply": "<?= __("Reply") ?>",
            "Write a message...": "<?= __("Write a message...") ?>",
            "Add": "<?= __("Add") ?>",
            "Input topic title": "<?= __("Input topic title") ?>",
            "Update": "<?= __("Update") ?>",
            // Message
            "No results found": "<?= __("No results found") ?>",
            "Leave this topic": "<?= __("Leave this topic") ?>",
            "Are you sure you want to leave this topic?": "<?= __("Are you sure you want to leave this topic?") ?>",
            "Do you really want to delete this comment?": "<?= __("Do you really want to delete this comment?") ?>",
            "Delete comment": "<?= __("Delete comment") ?>",
            "Search result %d topics hit": "<?= __("Search result %d topics hit") ?>",
            "Search result %d messages hit": "<?= __("Search result %d messages hit") ?>",
            "View the latest messages": "<?= __("View the latest messages") ?>",
            "member": "<?= __("member") ?>",
            "topic": "<?= __("topic") ?>",
            "message": "<?= __("message") ?>",
            "Topics": "<?= __("Topics") ?>",
            "Messages": "<?= __("Messages") ?>",
            "%d matched member": "<?= __("%d matched member") ?>",
            "%d matched members": "<?= __("%d matched members") ?>",
            "%d matched message": "<?= __("%d matched message") ?>",
            "%d matched messages": "<?= __("%d matched messages") ?>",
            /* Change to paid plan */
            // Select country and paymnet type
            "Select Country Location": "<?= __("Select Country Location") ?>",
            "Select Payment Method": "<?= __("Select Payment Method") ?>",
            "Credit Card": "<?= __("Credit Card") ?>",
            "Invoice": "<?= __("Invoice") ?>",
            "You can use Visa, MasterCard and AmericanExpress.": "<?= __("You can use Visa, MasterCard and AmericanExpress.") ?>",
            "Invoice will be issued monthly, so please transfer by the deadline.": "<?= __("Invoice will be issued monthly, so please transfer by the deadline.") ?>",
            "Setup": "<?= __("Setup") ?>",
            "Germany": "<?= __("Germany") ?>",
            "Japan": "<?= __("Japan") ?>",
            "Thailand": "<?= __("Thailand") ?>",
            "United States": "<?= __("United States") ?>",
            // Select campaign
            "%d members": "<?= __("%d members") ?>",
            "max members": "<?= __("max members") ?>",
            "per month": "<?= __("per month") ?>",
            "Price": "<?= __("Price") ?>",
            // Input company info
            "Enter Company Information": "<?= __("Enter Company Information") ?>",
            "Company Address": "<?= __("Company Address") ?>",
            "Company Name": "<?= __("Company Name") ?>",
            "ISAO Corporation": "<?= __("ISAO Corporation") ?>",
            "Post Code": "<?= __("Post Code") ?>",
            "12345 ": "<?= __("12345 ") ?>",
            "State/Province/Region": "<?= __("State/Province/Region") ?>",
            "California": "<?= __("California") ?>",
            "City": "<?= __("City") ?>",
            "Los Angeles": "<?= __("Los Angeles") ?>",
            "Street": "<?= __("Street") ?>",
            "1234 Street Name": "<?= __("1234 Street Name") ?>",
            "Company Contact": "<?= __("Company Contact") ?>",
            "eg. Jobs": "<?= __("eg. Jobs") ?>",
            "eg. Bruce": "<?= __("eg. Bruce") ?>",
            "Last Name ": "<?= __("Last Name ") ?>",
            "Last Name Kana": "<?= __("Last Name Kana") ?>",
            "First Name ": "<?= __("First Name ") ?>",
            "First Name Kana": "<?= __("First Name Kana") ?>",
            "name@company.com": "<?= __("name@company.com") ?>",
            "Telephone": "<?= __("Telephone") ?>",
            // Input credit card
            "Name on Card": "<?= __("Name on Card") ?>",
            "Card Number": "<?= __("Card Number") ?>",
            "Price per user": "<?= __("Price per user") ?>",
            "Number of users": "<?= __("Number of users") ?>",
            "Sub Total": "<?= __("Sub Total") ?>",
            "Tax": "<?= __("Tax") ?>",
            "Total": "<?= __("Total") ?>",
            "Enter your card information": "<?= __("Enter your card information") ?>",
            "I agree with the terms of service": "<?= __("I agree with the terms of service") ?>",
            "Register": "<?= __("Register") ?>",
            "Agree & Purchase": "<?= __("Agree & Purchase") ?>",
            // Input Billing info
            "Enter Billing Information": "<?= __("Enter Billing Information") ?>",
            "Same as company information": "<?= __("Same as company information") ?>",
            // Confirm
            "Terms of Use": "<?= __("Terms of Use") ?>",
            "Confirm registration and charge": "<?= __("Confirm registration and charge") ?>",

            // Complete
            "Thank You": "<?= __("Thank You") ?>",
            "Your transaction and registration to the payment plan was successful.": "<?= __("Your transaction and registration to the payment plan was successful.") ?>",
            "In the case of invoice payment, we conduct a credit check. As a result of the investigation, we will contact you if we deem it impossible to trade.": "<?= __("In the case of invoice payment, we conduct a credit check. As a result of the investigation, we will contact you if we deem it impossible to trade.") ?>",
            "Move to Billing page": "<?= __("Move to Billing page") ?>",
            // Invite
            "Update completed": "<?= __("Update completed") ?>",
            "Invite members": "<?= __("Invite members") ?>",
            "Email Address": "<?= __("Email Address") ?>",
            "You can set email addresses by comma(,) separated or by newline separated.": "<?= __("You can set email addresses by comma(,) separated or by newline separated.") ?>",
            "I confirmed the billing content": "<?= __("I confirmed the billing content") ?>",
            "days": "<?= __("days") ?>",
            "month": "<?= __("month") ?>",
            "people": "<?= __("people") ?>",
            "View details": "<?= __("View details") ?>",
            "Billing": "<?= __("Billing") ?>",
            "Total charge amount": "<?= __("Total charge amount") ?>",
            "Tax included": "<?= __("Tax included") ?>",
            "Tax excluded": "<?= __("Tax excluded") ?>",
            "Number of days": "<?= __("Number of days") ?>",
            "Daily payment": "<?= __("Daily payment") ?>",
            "Purchase & Invite": "<?= __("Purchase & Invite") ?>",
            "Purchase" : "<?= __("Purchase") ?>",
            "Only Kana characters are allowed.": "<?= __("Only Kana characters are allowed.") ?>",
            "Invalid fields": "<?= __("Invalid fields") ?>",
            "Contact Us": "<?= __("Contact Us") ?>",
            "Please inform about upgrade my Campaign Plan": "<?= __("Please inform about upgrade my Campaign Plan") ?>",
            // Goals
            "No Goals found": "<?= __("No Goals found") ?>",
            "Enter %2$d numeric characters for postal code.": "<?= __('Enter %2$d numeric characters for postal code.') ?>",
            // Campaigns
            "Select": "<?= __("Select") ?>",
            "Plan": "<?= __("Plan") ?>",
            "Upgrade Plan": "<?= __("Upgrade Plan") ?>",
            "Upgrade": "<?= __("Upgrade") ?>",
            "Select Plan": "<?= __("Select Plan") ?>",
            "This invitation will cause your team's active members to exceed the current plan limit. Please upgrade your plan.": "<?= __("This invitation will cause your team's active members to exceed the current plan limit. Please upgrade your plan.") ?>",
            "Larger plans available on request. All prices are without tax.": "<?= __("Larger plans available on request. All prices are without tax.") ?>",
            "You have %d active members. Please select the best plan for the number of members expected for your team.": "<?= __("You have %d active members. Please select the best plan for the number of members expected for your team.") ?>",
            "Campaign Contract": "<?= __("Campaign Contract") ?>",
            "By purchasing, you agree to the %s and %s.": "<?= __("By purchasing, you agree to the %s and %s.") ?>",
            "Contact us": "<?= __("Contact us") ?>",
            "Your team upgraded price plan": "<?= __("Your team upgraded price plan") ?>",
            "Current plan": "<?= __("Current plan") ?>",
            "Your monthly bill will increase from %s to %s. You'll be charged a prorated amount today, shown below, for this upgrade.": "<?= __("Your monthly bill will increase from %s to %s. You'll be charged a prorated amount today, shown below, for this upgrade.") ?>",
            "Annual plan(paid monthly)": "<?= __("Annual plan(paid monthly)") ?>",
            // Saved item
            "Remove": "<?= __("Remove") ?>",
            "SAVED ITEMS": "<?= __("SAVED ITEMS") ?>",
            "Actions": "<?= __("Actions") ?>",
            "Posts": "<?= __("Posts") ?>",
            "Circles": "<?= __("Circles") ?>",
            "Save item": "<?= __("Save item") ?>",
            "Save Actions and Posts that you want to see again. No one is notified, and only you can see what you’ve saved.": "<?= __("Save Actions and Posts that you want to see again. No one is notified, and only you can see what you’ve saved.") ?>",
            "Item not found": "<?= __("Item not found") ?>",
            "Item saved": "<?= __("Item saved") ?>",
            "Item removed": "<?= __("Item removed") ?>",
            // Search
            "Information matching the search could not be found": "<?= __("Information matching the search could not be found") ?>",
            "Search post and comment": "<?= __("Search post and comment") ?>",
            "Comments": "<?= __("Comments") ?>",
        },
        regex: {
            user_name: "<?= User::USER_NAME_REGEX_JAVASCRIPT ?>"
        },
        const: {
            USER_STATUS: {
                INVITED: "<?= TeamMember::USER_STATUS_INVITED ?>",
                ACTIVE: "<?= TeamMember::USER_STATUS_ACTIVE ?>",
                INACTIVE: "<?= TeamMember::USER_STATUS_INACTIVE ?>",
            },
        },
        notify_auto_update_sec: <?=NOTIFY_AUTO_UPDATE_SEC?>,
        new_notify_cnt: <?=isset($new_notify_cnt) ? $new_notify_cnt : 0?>,
        new_notify_message_cnt: <?=isset($new_notify_message_cnt) ? $new_notify_message_cnt : 0?>,
        common_form_type: "<?= isset($common_form_type) ? $common_form_type : null?>",
        request_params: <?=json_encode($this->request->params)?>,
        is_mb_app: "<?= $is_mb_app ?>",
        is_mb_browser: "<?= $isMobileBrowser ?>",
        is_mb_app_ios: "<?= $is_mb_app_ios ?>",
        is_mb_app_ios_high_header: "<?= $is_mb_app_ios_high_header ?>",
        is_mb_app_web_footer: "<?= $is_mb_app_web_footer ?>",
        pre_file_ttl: <?= PRE_FILE_TTL ?>,
        notify_setting: <?= isset($notify_setting) ? json_encode($notify_setting) : "''" ?>,
        unread_msg_topic_ids: <?=isset($unread_msg_topic_ids) ? json_encode($unread_msg_topic_ids) : "''"?>,
        select2_query_limit: <?=SELECT2_QUERY_LIMIT?>,
        current_term_start_date_format: "<?= viaIsSet($current_term_start_date_format) ?>",
        current_term_end_date_format: "<?= viaIsSet($current_term_end_date_format) ?>",
        attachable_max_file_size_mb: "<?= AttachedFile::ATTACHABLE_MAX_FILE_SIZE_MB?>"
    };

    function __(text) {
        if (cake.translation[text] !== undefined) {
            return cake.translation[text];
        }
        return text;
    }

    function sprintf() {
        var args = arguments,
            string = args[0],
            i = 1;
        return string.replace(/%((%)|s|d)/g, function (m) {
            // m is the matched format, e.g. %s, %d
            var val = null;
            if (m[2]) {
                val = m[2];
            } else {
                val = args[i];
                // A switch statement so that the formatter can be extended. Default is %s
                switch (m) {
                    case '%d':
                        val = parseFloat(val);
                        if (isNaN(val)) {
                            val = 0;
                        }
                        break;
                }
                i++;
            }
            return val;
        });
    }

</script>
<?= $this->App->viewEndComment() ?>

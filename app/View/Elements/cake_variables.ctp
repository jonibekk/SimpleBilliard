<?php
/**
 * Created by PhpStorm.
 * User: daikihirakata
 * Date: 7/21/16
 * Time: 00:40
 */
?>
<?= $this->App->viewStartComment() ?>
<script type="text/javascript">
    var cake = {
        env_name: "<?= ENV_NAME ?>",
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
                dropzone_uploading_not_end: "<?=__('Not finished uploading yet, please wait just a moment.')?>",
                dropzone_uploaded_file_expired: "<?=__('Uploaded files are invalid due. Please try again.')?>",
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
                leave_secret_circle: "<?=__('After leaving secret circle, you can\'t join again by yourself. Do you really want to leave this secret circle?')?>"
            },
            info: {
                a: "<?=__("Copied the post URL to the clipboard.")?>",
                b: "<?=__("Loading...")?>",
                c: "<?=__("Searching...")?>",
                d: "<?=__("Following")?>",
                e: "<?=__("More") ?> ",
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
            k: "<?=__("Select a key result (optional)")?>",
            l: "<?=__("No key result")?>",
            select_notify_range: "<?=__("Add notified parties (optional)")?>",
            public: "<?=__("Public")?>",
            secret: "<?=__("Secret")?>",
            select_public_circle: "<?=__("Choose public circle(s) or member(s)")?>",
            select_public_message: "<?=__("Choose member(s)")?>",
            select_secret_circle: "<?=__("Choose secret circle(s)")?>",
            share_change_disabled: "<?=__("You can't switch in the circle page.")?>",
            success: "<?=__("Success")?>",
            error: "<?=__("Error")?>",
            cancel: "<?=__("Cancel")?>",
            search_placeholder_user: "<?=__("Name?")?>",
            search_placeholder_goal: "<?=__("Goal Name?")?>",
            search_placeholder_circle: "<?=__("Circle Name?")?>",
            leave_circle: "<?=__("Leave circle.")?>",
            config: "<?=__("Settings")?>",
            waiting_message: "<?=__("Ironing your data. Please wait...")?>"
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
            o: "<?=$this->Html->url(['controller' => 'teams', 'action' => 'ajax_set_current_team_active_flag'])?>/",
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
            ah: "<?=$this->Html->url(['controller' => 'posts', 'action' => 'ajax_get_message'])?>/",
            ai: "<?=$this->Html->url(['controller' => 'posts', 'action' => 'ajax_put_message'])?>/",
            aj: "<?=$this->Html->url(['controller' => 'posts', 'action' => 'ajax_get_message_info'])?>/",
            ak: "<?=$this->Html->url(['controller' => 'posts', 'action' => 'ajax_put_message_read'])?>/",
            al: "<?=$this->Html->url(['controller' => 'posts', 'action' => 'ajax_get_message_list'])?>/",
            am: "<?=$this->Html->url(['controller' => 'teams', 'action' => 'ajax_invite_setting'])?>/",
            add_member_on_message: "<?=$this->Html->url([
                'controller' => 'users',
                'action'     => 'ajax_select_only_add_users'
            ])?>",
            select2_secret_circle: "<?=$this->Html->url([
                'controller' => 'users',
                'action'     => 'ajax_select2_get_secret_circles'
            ])?>/",
            select2_circle_user: "<?=$this->Html->url([
                'controller' => 'users',
                'action'     => 'ajax_select2_get_circles_users'
            ])?>",
            select2_goals: "<?=$this->Html->url(['controller' => 'goals', 'action' => 'ajax_select2_goals'])?>",
            select2_circles: "<?=$this->Html->url(['controller' => 'circles', 'action' => 'ajax_select2_circles'])?>",
            user_page: "<?= $this->Html->url(['controller' => 'users', 'action' => 'view_goals', 'user_id' => '']) ?>",
            goal_page: "<?= $this->Html->url(['controller' => 'goals', 'action' => 'view_info', 'goal_id' => '']) ?>",
            circle_page: "<?= $this->Html->url(['controller' => 'posts', 'action' => 'feed', 'circle_id' => '']) ?>/",
            goal_followers: "<?=$this->Html->url(['controller' => 'goals', 'action' => 'ajax_get_followers'])?>",
            goal_members: "<?= $this->Html->url(['controller' => 'goals', 'action' => 'ajax_get_members']) ?>",
            goal_key_results: "<?= $this->Html->url(['controller' => 'goals', 'action' => 'ajax_get_key_results']) ?>",
            upload_file: "<?= $this->Html->url(['controller' => 'posts', 'action' => 'ajax_upload_file']) ?>",
            remove_file: "<?= $this->Html->url(['controller' => 'posts', 'action' => 'ajax_remove_file']) ?>",
            message_list: "<?= $this->Html->url(['controller' => 'posts', 'action' => 'message_list']) ?>",
            ajax_message_list: "<?= $this->Html->url(['controller' => 'posts', 'action' => 'ajax_message_list']) ?>",
            ajax_message: "<?= $this->Html->url(['controller' => 'posts', 'action' => 'ajax_message']) ?>",
            invite_member: "<?= $this->Html->url([
                'controller' => 'teams',
                'action'     => 'settings',
                '#'          => 'invite_member'
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
            timezones: <?= isset($timezones) ? json_encode($timezones) : "''" ?>
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
            'Requires Android 4.4 or later.': "<?= __('Requires Android 4.4 or later.') ?>",
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
            "Create": "<?= __("Create") ?>",
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
            'Make a good goal to discuss with your project members.': "<?= __('Make a good goal to discuss with your project members.') ?>",
            'Have a common goal': "<?= __('Have a common goal') ?>",
            'Action for your goal': "<?= __('Action for your goal') ?>",
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
            "We've sent a six-digit confirmation code to %s. Enter it below to confirm your email address.": "<?= __("We've sent a six-digit confirmation code to %s. Enter it below to confirm your email address.",
                $signup_inputed_email = isset($signup_inputed_email) ? $signup_inputed_email : '')?>",
            "What's your name?": "<?= __("What's your name?") ?>",
            "Your name will only be displayed to your team on Goalous.": "<?= __("Your name will only be displayed to your team on Goalous.") ?>",
            "I want to receive news and updates by email from Goalous.": "<?= __("I want to receive news and updates by email from Goalous.") ?>",
            "Set your password": "<?= __("Set your password") ?>",
            "Create your password to login to Goalous.": "<?= __("Create your password to login to Goalous.") ?>",
            "Please don't close this window.": "<?= __("Please don't close this window.") ?>",
            "What do you want to call your Goalous team?": "<?= __("What do you want to call your Goalous team?") ?>",
            "Create a name for your team. A team is a group that can share goals, actions and posts  with each other. People outside of the team can't access this information.": "<?= __("Create a name for your team. A team is a group that can share goals, actions and posts  with each other. People outside of the team can't access this information.") ?>",
            "Use 8 or more characters including at least one number.": "<?= __("Use 8 or more characters including at least one number.") ?>",
            "Choose your team's (company's) term": "<?=__("Choose your team's (company's) term") ?>",
            "Set the term for your team. The term can be based on your corporate / financial calendar, personal evaluations or any period of time the works best for your company.": "<?= __("Set the term for your team. The term can be based on your corporate / financial calendar, personal evaluations or any period of time the works best for your company.") ?>",
            "Your name": "<?= __("Your name") ?>",
            "Birthday": "<?= __("Birthday") ?>",
            "I agree to %s and %s of Goalous.": '<?= __("I agree to %s and %s of Goalous.",
                '<a href="/terms" target="_blank" class="signup-privacy-policy-link">' . __("Terms of Service") . '</a>',
                '<a href="/privacy_policy" target="_blank" class="signup-privacy-policy-link">' . __("Privacy Policy") . '</a>') ?>',
            "eg. Harry": "<?= __("eg. Harry") ?>",
            "eg. Armstrong": "<?= __("eg. Armstrong") ?>",
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
            "Do you want to evaluate this goal ?": "<?=__("Do you want to evaluate this goal ?")?>",
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
            "Confirm this goal": "<?= __("Confirm this goal") ?>",
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
            "Wish goal approval": "<?=__("Wish goal approval")?>",
            "Name": "<?=__("Name")?>",
            "Level of achievement": "<?=__("Level of achievement")?>",
            "Achieved": "<?= __("Achieved") ?>",
            "Unachieved": "<?= __("Unachieved") ?>"
        },
        regex: {
            user_name: "<?= User::USER_NAME_REGEX ?>"
        },
        notify_auto_update_sec: <?=NOTIFY_AUTO_UPDATE_SEC?>,
        new_notify_cnt: <?=isset($new_notify_cnt) ? $new_notify_cnt : 0?>,
        new_notify_message_cnt: <?=isset($new_notify_message_cnt) ? $new_notify_message_cnt : 0?>,
        common_form_type: "<?= isset($common_form_type) ? $common_form_type : null?>",
        request_params: <?=json_encode($this->request->params)?>,
        is_mb_app: "<?= $is_mb_app ?>",
        is_mb_app_ios: "<?= $is_mb_app_ios ?>",
        pre_file_ttl: <?= PRE_FILE_TTL ?>,
        notify_setting: <?= isset($notify_setting) ? json_encode($notify_setting) : "''" ?>,
        unread_msg_post_ids: <?=isset($unread_msg_post_ids) ? json_encode($unread_msg_post_ids) : "''"?>,
        select2_query_limit: <?=SELECT2_QUERY_LIMIT?>,
        current_term_start_date_format: "<?= viaIsSet($current_term_start_date_format) ?>",
        current_term_end_date_format: "<?= viaIsSet($current_term_end_date_format) ?>",
    };

    function __(text) {
        if (cake.translation[text] !== undefined) {
            return cake.translation[text];
        }
        return text;
    }

    <?php if(isset($mode_view)):?>
    <?php if($mode_view == MODE_VIEW_TUTORIAL):?>
    $("#modal_tutorial").modal('show');
    <?php endif;?>
    <?php endif;?>
</script>
<?= $this->App->viewEndComment() ?>

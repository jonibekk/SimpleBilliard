<!-- START app/View/Teams/member_list.ctp -->
<style type="text/css">
    .team_member_table {
        background-color: #ffffff;
        font-size: 14px;
        border-radius: 5px;
    }

    .team_member_count_label {
        font-size: 18px;
        padding: 12px;
    }

    .team_member_setting_btn {
        color: #ffffff;
        background-color: #ffffff;
        border-color: lightgray;
    }

</style>

<script type="text/javascript">
    function changeFilter() {
        var filter_name = document.getElementById('filter_name').selectedIndex;
        if (filter_name == 2) {
            document.getElementById('name_field').style.display = "none";
            document.getElementById('group_field').style.display = "block";

        } else {
            document.getElementById('name_field').style.display = "block";
            document.getElementById('group_field').style.display = "none";
        }
    }
</script>

<br>
<div class="well">
    <div id="filter_box" class="row">
        <div class="col-xs-12 col-sm-4">
            <select id="filter_name" name="filter_name" class="form-control" onchange="changeFilter()">
                <option value="all">すべて</option>
                <option value="coach_name">コーチの名前</option>
                <option value="group_name">グループ名</option>
                <option value="team_admin">チーム管理者</option>
                <?php if($login_user_admin_flg === true) { ?>
                <option value="invite">招待中</option>
                <option value="two_step">2段階認証OFF</option>
                <?php } ?>
            </select>
        </div>
        <div class="col-xs-12 col-sm-8">
            <div id="name_field">
                <input type="text" class="form-control" placeholder="名前を入力してください">
            </div>
            <div id="group_field" style="display: none">
                <select name="group_id" class="form-control">
                    <option value="all">すべて</option>
                    <?php foreach ($group_info as $id => $name) { ?>
                        <option value="<?php echo $id; ?>"><?php echo $name; ?></option>
                    <?php } ?>
                </select>
            </div>
        </div>
    </div>
    <div class="checkbox">
        <label>
            <input type="checkbox" value="">
            無効化されたメンバーも表示する
            <!-- <p><?= $ui['TeamMember']['active_flg'] == (string)TeamMember::ACTIVE_USER_FLAG ? '在職中' : '退職'; ?></p> -->
        </label>
    </div>
</div>

<div class="row">
    <div class="team_member_count_label">対象メンバー ( <?= $count; ?> )</div>
    <div class="col-xs-12">
        <table class="table team_member_table">
            <?php foreach ($user_info as $key => $ui) { ?>
                <tr>
                    <td width="30%">
                        <?= $this->Html->image('ajax-loader.gif', ['class'         => 'lazy comment-img',
                                                                   'data-original' => $this->Upload->uploadUrl($ui['User'],
                                                                                                               'User.photo',
                                                                                                               ['style' => 'small'])]) ?>
                        <p>
                            <?= $ui['User']['display_username']; ?>
                            <?php if ($ui['TeamMember']['admin_flg'] == (string)TeamMember::ADMIN_USER_FLAG) { ?> <i
                                class="fa fa-adn"></i> <?php } ?>
                        </p>
                    </td>

                    <td>
                        <p><i class="fa fa-sitemap"></i> グループ名</p>
                        <p><i class="fa fa-venus-double"></i> <?php if (isset($ui['TeamMember']['coach_name'])) { ?>
                                <?= $ui['TeamMember']['coach_name']; ?><?php } else { ?>コーチはいません<?php } ?></p>
                        <?php if($login_user_admin_flg === true) { ?>
                        <p><i class="fa fa-lock"></i> <?= is_null($ui['User']['2fa_secret']) == true ? 'OFF' : 'ON'; ?>
                        </p>
                        <p><i class="fa fa-shield"></i> <?php if ($ui['TeamMember']['evaluation_enable_flg'] == true) { ?>評価対象者です
                            <?php } else { ?>評価対象者ではありません<?php } ?></p>
                        <?php } ?>
                    </td>

                    <?php if($login_user_admin_flg === true) { ?>
                    <td width="20%">
                        <div class="pull-right header-function dropdown">
                            <button class="btn team_member_setting_btn dropdown-toggle" type="button" id="dropdownMenu1"
                                    data-toggle="dropdown" aria-expanded="true">
                                <i class="fa fa-cog header-function-icon"
                                   style="color: rgb(80, 80, 80); opacity: 0.88;"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-right frame-arrow-icon" role="menu"
                                aria-labelledby="dropdownMenu1">
                                <li role="presentation"><a role="menuitem" tabindex="-1" href="#">非アクティブにする</a></li>
                                <li role="presentation"><a role="menuitem" tabindex="-1" href="#">アクティブにする</a></li>
                                <li role="presentation"><a role="menuitem" tabindex="-1" href="#">管理者にする</a></li>
                                <li role="presentation"><a role="menuitem" tabindex="-1" href="#">管理者から外す</a></li>
                            </ul>
                        </div>
                    </td>
                    <?php } ?>
                </tr>
            <?php } ?>
        </table>
    </div>
</div>

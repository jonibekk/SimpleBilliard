<!-- START app/View/Teams/member_list.ctp -->
<style type="text/css">
    .team_member_table {
        background-color: #ffffff;
        font-size: 14px;
        border-radius: 5px;
    }

    .team_member_count_label {
        font-size: 24px;
        padding: 12px;
    }

    .team_member_setting_btn {
        color: #ffffff;
        background-color: #696969;
        border-color: #696969;
    }

</style>

<script type="text/javascript">
    function changeFilter() {
        var filter_name = document.getElementById('filter_name').selectedIndex;
        if (filter_name == 0) {
            document.getElementById('name_field').style.display = "block";
            document.getElementById('admin_field').style.display = "none";
            document.getElementById('coach_field').style.display = "none";
            document.getElementById('group_field').style.display = "none";

        } else if (filter_name == 1) {
            document.getElementById('name_field').style.display = "none";
            document.getElementById('admin_field').style.display = "block";
            document.getElementById('coach_field').style.display = "none";
            document.getElementById('group_field').style.display = "none";

        } else if (filter_name == 2) {
            document.getElementById('name_field').style.display = "none";
            document.getElementById('admin_field').style.display = "none";
            document.getElementById('coach_field').style.display = "block";
            document.getElementById('group_field').style.display = "none";

        } else if (filter_name == 3) {
            document.getElementById('name_field').style.display = "none";
            document.getElementById('admin_field').style.display = "none";
            document.getElementById('coach_field').style.display = "none";
            document.getElementById('group_field').style.display = "block";

        }
    }
</script>

<div class="well">
    <div id="filter_box" class="row">
        <div class="col-xs-12 col-sm-4">
            <select id="filter_name" name="filter_name" class="form-control" onchange="changeFilter()">
                <option value="name">名前</option>
                <option value="admin">管理者</option>
                <option value="coach">コーチ</option>
                <option value="group">グループ</option>
            </select>
        </div>
        <div class="col-xs-12 col-sm-8">
            <div id="name_field">
                <input type="text" class="form-control">
            </div>
            <div id="admin_field" style="display: none">
                <select name="admin_flg" class="form-control">
                    <option value="">管理者のみ</option>
                    <option value="">一般メンバーのみ</option>
                </select>
            </div>
            <div id="coach_field" style="display: none">
                <select name="coach_id" class="form-control">
                    <option value="">菊池</option>
                    <option value="">草刈</option>
                </select>
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
</div>



<div class="row">
    <div class="team_member_count_label"><?= $count; ?> people in your network</div>
    <div class="col-xs-12">
        <table class="table table-bordered team_member_table">
            <?php foreach ($user_info as $key => $ui) { ?>
                <tr>
                    <td width="30%">
                        <?= $this->Html->image('ajax-loader.gif', ['class'         => 'lazy comment-img',
                                                                   'data-original' => $this->Upload->uploadUrl($ui['User'],
                                                                                                               'User.photo',
                                                                                                               ['style' => 'small'])]) ?>
                        <p><?= $ui['User']['display_username']; ?></p>
                    </td>
                    <td>

                        <p><?= $ui['TeamMember']['admin_flg'] == (string)TeamMember::ADMIN_USER_FLAG ? '管理者' : 'メンバー'; ?></p>

                        <p><?= $ui['TeamMember']['active_flg'] == (string)TeamMember::ACTIVE_USER_FLAG ? '在職中' : '退職'; ?></p>

                        <p><?= is_null($ui['User']['2fa_secret']) == true ? '2段階認証未設定' : '2段階認証済み'; ?></p>
                    </td>
                    <td width="20%">
                        <div class="dropdown">
                            <button class="btn btn-sm team_member_setting_btn dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-expanded="true">
                                設定 <span class="caret"></span>
                            </button>
                            <ul class="dropdown-menu" role="menu" aria-labelledby="dropdownMenu1">
                                <li role="presentation"><a role="menuitem" tabindex="-1" href="#">非アクティブにする</a></li>
                                <li role="presentation"><a role="menuitem" tabindex="-1" href="#">アクティブにする</a></li>
                                <li role="presentation"><a role="menuitem" tabindex="-1" href="#">管理者にする</a></li>
                                <li role="presentation"><a role="menuitem" tabindex="-1" href="#">管理者から外す</a></li>
                            </ul>
                        </div>
                    </td>
                </tr>
            <?php } ?>
        </table>
    </div>
</div>

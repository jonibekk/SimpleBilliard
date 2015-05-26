<!-- START app/View/Teams/member_list.ctp -->
<script type="text/javascript">
    function changeFilter() {
        var filter_name = document.forms.TeamMemberListForm.filter_name.selectedIndex;
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
    <div class="row">
        <?= $this->Form->create(); ?>
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
                    <?php foreach ($group_info as $id => $name) { ?>
                        <option value="<?php echo $id; ?>"><?php echo $name; ?></option>
                    <?php } ?>
                </select>
            </div>
        </div>
        <?= $this->Form->end(); ?>
    </div>
</div>

<div class="row">
    <div class="col-xs-12">
        <table class="table table-striped">
            <?php foreach ($user_info as $key => $ui) { ?>
                <tr>
                    <td width="30%">
                        <?= $this->Html->image('ajax-loader.gif', ['class'         => 'lazy comment-img',
                                                                   'data-original' => $this->Upload->uploadUrl($ui['User']['id'],
                                                                                                               'User.photo',
                                                                                                               ['style' => 'small'])]) ?>
                    </td>
                    <td>
                        <p><?= $ui['User']['display_username']; ?></p>

                        <p><?= $ui['TeamMember']['admin_flg'] == (string)TeamMember::ADMIN_USER_FLAG ? '管理者' : 'メンバー'; ?></p>

                        <p><?= $ui['TeamMember']['active_flg'] == (string)TeamMember::ACTIVE_USER_FLAG ? '在職中' : '退職'; ?></p>

                        <p><?= is_null($ui['User']['2fa_secret']) == true ? '2段階認証未設定' : '2段階認証済み'; ?></p>
                    </td>
                </tr>
            <?php } ?>
        </table>
    </div>
</div>

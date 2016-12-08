<?= $this->App->viewStartComment() ?>
<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close font_33px close-design" data-dismiss="modal" aria-hidden="true"><span
                    class="close-icon">&times;</span></button>
            <h4 class="modal-title"><?= __("Change leader") ?></h4>
        </div>
        <?=
        $this->Form->create('GoalMember', [
            'url'           => [
                'controller'    => 'goals',
                'action'        => $isLeader ? 'exchange_leader_by_leader' : 'assign_leader_by_goal_member',
            ],
            'inputDefaults' => [
                'div'       => 'form-group',
                'label'     => [
                    'class' => 'control-label text-align_left'
                ],
                'class'     => 'form-control modal_input-design'
            ],
            'class'         => 'form-horizontal js-exchange-leader-form',
            'data-bv-live'  => 'disabled',
            'novalidate'    => true,
        ]); ?>
        <?= $this->Form->hidden('Goal.id', ['value' => $goalId]); ?>
        <div class="modal-body">
            <div class="row">
                <h5 class="modal-key-result-headings"><?= __("Leader") ?></h5>
                <p class="mb_8px"><?= __("Select the leader name you want to change.")?></p>
                <?= $this->Form->input('NewLeader.id',
                    [
                        'label'               => false,
                        'type'                => 'select',
                        'class'               => 'form-control',
                        'required'            => true,
                        'options'             => $goalMembers
                    ]) ?>

                <?php if ($isLeader && $currentLeader): ?>
                    <p class="mb_8px"><?= __("Current")?> : <?= Hash::get($currentLeader, 'User.display_username') ?></p>
                    <hr>
                    <h5 class="modal-key-result-headings"><?= __("Collaborator") ?></h5>
                    <p class="mb_8px"><?= __("Select whether you collaborate or quit this goal.")?></p>
                    <?= $this->Form->hidden('id', ['value' => Hash::get($currentLeader, 'GoalMember.id')]); ?>
                    <?=
                    $this->Form->input('GoalMember.role',
                        [
                            'label'                        => __("Role"),
                            'placeholder'                  => __("eg) Increasing the number of users"),
                            "data-bv-notempty-message"     => __("Input is required."),
                            'data-bv-stringlength'         => 'true',
                            'data-bv-stringlength-max'     => 200,
                            'data-bv-stringlength-message' => __("It's over limit characters (%s).", 200),
                            'required'                     => true,
                            'rows'                         => 1
                        ]) ?>
                    <?=
                    $this->Form->input('GoalMember.description',
                        [
                            'label'                        => __("Description"),
                            'placeholder'                  => __("eg) I will get the users by advertising."),
                            "data-bv-notempty-message"     => __("Input is required."),
                            'data-bv-stringlength'         => 'true',
                            'data-bv-stringlength-max'     => 2000,
                            'data-bv-stringlength-message' => __("It's over limit characters (%s).", 2000),
                            'required'                     => true,
                            'rows'                         => 1
                        ]) ?>
                    <?=
                    $this->Form->input('GoalMember.priority',
                        [
                            'label'                    => __("Weight"),
                            "data-bv-notempty-message" => __("Input is required."),
                            'required'                 => true,
                            'type'                     => 'select',
                            'default'                  => 3,
                            'options'                  => $priorityList
                        ]) ?>
                    <?= $this->Form->button(__("Save & Collaborate"), [
                        'class' => 'btn btn-fullsize-active mt_12px',
                        'name'  => 'change_type',
                        'value' => GoalMemberService::CHANGE_LEADER_WITH_COLLABORATION,
                    ]) ?>
                    <div class="text-on-the-line-box">
                        <p class="text">OR</p>
                        <hr>
                    </div>
                    <?= $this->Form->button(__("Save & Quit this goal"), [
                        'class' => 'btn btn-fullsize-active',
                        'name'  => 'change_type',
                        'value' => GoalMemberService::CHANGE_LEADER_WITH_QUIT,
                    ]) ?>
                <?php else: ?>
                <?php // コラボレーターの場合(リーダー不在) ?>
                    <?= $this->Form->button(__("Save"), [
                        'class' => 'btn btn-fullsize-active mt_12px',
                        'name'  => 'change_type',
                        'value' => GoalMemberService::CHANGE_LEADER_FROM_GOAL_MEMBER,
                    ]) ?>
                <?php endif; ?>
            </div>
        </div>
        <script type="text/javascript">
            $(document).ready(function () {
                $('.js-exchange-leader-form').bootstrapValidator({
                    live: 'enabled',
                    fields: {}
                }).on('click', 'button[value="<?= GoalMemberService::CHANGE_LEADER_WITH_QUIT ?>"]', function () {
                    $('.js-exchange-leader-form').bootstrapValidator('destroy');
                });
            });
        </script>
        <?= $this->Form->end() ?>
    </div>
</div>
<?= $this->App->viewEndComment() ?>

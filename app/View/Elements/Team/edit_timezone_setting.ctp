<?= $this->App->viewStartComment() ?>
<div class="panel panel-default">
    <div class="panel-heading"><?= __("Timezone setting") ?></div>
    <?=
    $this->Form->create('Team', [
        'inputDefaults' => [
            'div'       => 'form-group',
            'label'     => [
                'class' => 'col col-sm-3 control-label form-label'
            ],
            'wrapInput' => 'col col-sm-6',
            'class'     => 'form-control addteam_input-design'
        ],
        'class'         => 'form-horizontal',
        'novalidate'    => true,
        'type'          => 'file',
        'id'            => 'AddTeamForm',
        'url'           => ['action' => 'edit_timezone']
    ]); ?>
    <div class="panel-body add-team-panel-body">
        <?=
        $this->Form->input('timezone', [
            'label'      => __("Timezone"),
            'type'       => 'select',
//            'options'    => $Team,
        ]) ?>
    </div>

    <div class="panel-footer addteam_pannel-footer">
        <div class="row">
            <div class="col-xxs-4 col-sm-offset-3">
                <?=
                $this->Form->submit(__("Save settings"),
                    ['class' => 'btn btn-primary display-inline', 'div' => false]) ?>
            </div>
        </div>
    </div>
    <?= $this->Form->end(); ?>
</div>
<?= $this->App->viewEndComment() ?>

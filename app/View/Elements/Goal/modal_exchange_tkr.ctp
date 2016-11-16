<?= $this->App->viewStartComment() ?>
<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close font_33px close-design" data-dismiss="modal" aria-hidden="true"><span
                    class="close-icon">&times;</span></button>
            <h4 class="modal-title"><?= __("Change Top KR") ?></h4>
        </div>
        <?=
        $this->Form->create('KeyResult', [
            'inputDefaults' => [
                'div'       => 'form-group',
                'label'     => [
                    'class' => 'no-asterisk'
                ],
            ],
            'class'         => 'form-horizontal',
            'url'           => [
                'controller'    => 'goals',
                'action'        => 'exchange_tkr',
            ],
            'novalidate'    => true,
            'id'            => '',
        ]); ?>
        <div class="modal-body">
            <div class="row">
                <h5 class="modal-key-result-headings"><?= __("Top KR") ?></h5>
                <p class="mb_8px"><?= __("Select Top KR you would like to change.")?></p>
                <?= $this->Form->input('id',
                    [
                        'label'               => false,
                        'type'                => 'select',
                        'class'               => 'form-control',
                        'required'            => true,
                        'options'             => $krs
                    ]) ?>
                <p class="mt_8px"><?= __("Current")?>：<?= $tkr['name']?></p>
            </div>
        </div>
        <div class="modal-footer">
            <?php $btnLabel = $isApproval ? __("Save & Reapply") : __("Save changes");?>
            <?=
            $this->Form->submit($btnLabel,
                ['class' => 'btn btn-primary', 'div' => false]) ?>

            <button type="button" class="btn btn-default" data-dismiss="modal"><?= __("Close") ?></button>
        </div>
        <?= $this->Form->end() ?>
    </div>
</div>
<?= $this->App->viewEndComment() ?>

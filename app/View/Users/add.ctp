<?
/**
 * @var $this View
 */
?>
<div>
    <?=
    $this->Form->create('BoostCake', [
        'inputDefaults' => [
            'div'       => 'form-group',
            'label'     => [
                'class' => 'col col-md-3 control-label'
            ],
            'wrapInput' => 'col col-md-9',
            'class'     => 'form-control'
        ],
        'class'         => 'well form-horizontal'
    ]); ?>
    <fieldset>
        <legend><?= __('Add User'); ?></legend>
        <?
        echo $this->Form->input('password');
        echo $this->Form->input('email');
        echo $this->Form->input('local_first_name');
        echo $this->Form->input('local_last_name');
        echo $this->Form->input('first_name');
        echo $this->Form->input('last_name');
        ?>
    </fieldset>
    <div class="form-group">
        <?=
        $this->Form->submit(__('Submit'), [
            'div'   => 'col col-md-9 col-md-offset-3',
            'class' => 'btn btn-default'
        ]); ?>
    </div>
    <?= $this->Form->end(); ?>
</div>
<div class="actions">
    <h3><?= __('Actions'); ?></h3>
    <ul>

        <li><?= $this->Html->link(__('List Users'), ['action' => 'index']); ?></li>
    </ul>
</div>
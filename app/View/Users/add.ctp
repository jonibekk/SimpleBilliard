<div>
    <?php echo $this->Form->create('BoostCake', array(
        'inputDefaults' => array(
            'div'       => 'form-group',
            'label'     => array(
                'class' => 'col col-md-3 control-label'
            ),
            'wrapInput' => 'col col-md-9',
            'class'     => 'form-control'
        ),
        'class'         => 'well form-horizontal'
    )); ?>
    <fieldset>
        <legend><?php echo __('Add User'); ?></legend>
        <?php
        echo $this->Form->input('password');
        echo $this->Form->input('email');
        echo $this->Form->input('local_first_name');
        echo $this->Form->input('local_last_name');
        echo $this->Form->input('first_name');
        echo $this->Form->input('last_name');
        ?>
    </fieldset>
    <div class="form-group">
        <?php echo $this->Form->submit(__('Submit'), array(
            'div'   => 'col col-md-9 col-md-offset-3',
            'class' => 'btn btn-default'
        )); ?>
    </div>
    <?php echo $this->Form->end(); ?>
</div>
<div class="actions">
    <h3><?php echo __('Actions'); ?></h3>
    <ul>

        <li><?php echo $this->Html->link(__('List Users'), array('action' => 'index')); ?></li>
    </ul>
</div>
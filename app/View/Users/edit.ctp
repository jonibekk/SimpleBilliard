<?php
/**
 * @var $this View
 */
?>
<div class="users form">
    <?php echo $this->Form->create('User'); ?>
    <fieldset>
        <legend><?php echo __('Edit User'); ?></legend>
        <?php
        echo $this->Form->input('id');
        echo $this->Form->input('password');
        echo $this->Form->input('email');
        echo $this->Form->input('local_first_name');
        echo $this->Form->input('local_last_name');
        echo $this->Form->input('first_name');
        echo $this->Form->input('last_name');
        ?>
    </fieldset>
    <?php echo $this->Form->end(__('Submit')); ?>
</div>
<div class="actions">
    <h3><?php echo __('Actions'); ?></h3>
    <ul>

        <li><?php echo $this->Form->postLink(__('Delete'), ['action' => 'delete', $this->Form->value('User.id')],
                                             null, __('Are you sure you want to delete # %s?',
                                                      $this->Form->value('User.id'))); ?></li>
        <li><?php echo $this->Html->link(__('List Users'), ['action' => 'index']); ?></li>
    </ul>
</div>

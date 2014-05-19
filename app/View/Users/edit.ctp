<?
/**
 * @var $this View
 */
?>
<div class="users form">
    <?= $this->Form->create('User'); ?>
    <fieldset>
        <legend><?= __('Edit User'); ?></legend>
        <?
        echo $this->Form->input('id');
        echo $this->Form->input('password');
        echo $this->Form->input('email');
        echo $this->Form->input('local_first_name');
        echo $this->Form->input('local_last_name');
        echo $this->Form->input('first_name');
        echo $this->Form->input('last_name');
        ?>
    </fieldset>
    <?= $this->Form->end(__('Submit')); ?>
</div>
<div class="actions">
    <h3><?= __('Actions'); ?></h3>
    <ul>

        <li><?=
            $this->Form->postLink(__('Delete'), ['action' => 'delete', $this->Form->value('User.id')],
                                  [], __('Are you sure you want to delete # %s?',
                                         $this->Form->value('User.id'))); ?></li>
        <li><?= $this->Html->link(__('List Users'), ['action' => 'index']); ?></li>
    </ul>
</div>

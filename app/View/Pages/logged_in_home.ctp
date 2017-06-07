<?php /**
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.View.Pages
 * @since         CakePHP(tm) v 0.10.0.1076
 * @var CodeCompletionView $this
 */
?>
<?= $this->App->viewStartComment()?>
<?php if ($this->Session->read('current_team_id')): ?>
    <?= $this->element('Feed/contents') ?>
<?php else: ?>
    <?= $this->Html->link(__("Create a team."), ['controller' => 'teams', 'action' => 'add']) ?>
<?php endif; ?>
<?= $this->App->viewEndComment()?>


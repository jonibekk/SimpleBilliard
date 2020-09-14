<?php
if (!isset($form)) {
    $form = true;
}
$see_gka = isset($see_gka) ? $see_gka : false;
?>
<?= $this->App->viewStartComment() ?>
<section class="panel panel-default">
    <header>
        <h2><?= __("See GKA") ?></h2>
    </header>
    <div class="panel-body form-horizontal">
        <p><?= __("Can see GKA of all team members.") ?></p>
        <p><?= __("Learn More") ?></p>
        <?=
            $this->Form->create('Team', [
                'inputDefaults' => [
                    'div'       => false,
                    'label'     => false,
                    'class'     => 'bt-switch'
                ],
                'class'         => 'form-horizontal',
                'novalidate'    => true,
                'id'            => 'see_gka',
                'url'           => ['controller' => 'teams', 'action' => 'toggle_see_gka']
            ]); ?>
        <fieldset>
            <?= $this->Form->input("see_gka", ['type' => 'checkbox', 'default' => $see_gka, 'id' => 'see-gka-checkbox']) ?>
        </fieldset>
    </div>
    <footer>
        <button type="button" class="btn btn-primary" id='see-gka-btn'>
            <?= __('Save settings') ?>
        </button>
    </footer>
    <?= $this->element('Team/cannot_toggle_see_gka_modal') ?>
    <?= $this->element('Team/see_gka_toggle_off_modal') ?>
    <?= $this->element('Team/see_gka_toggle_on_modal') ?>
    <?= $this->Form->end() ?>
</section>
<?php $this->start('script') ?>
<script type="text/javascript">
    (function() {
        $('#see-gka-btn').click(function() {
            if (<?= $can_update_see_gka ?> === 1) {
                var checkbox = document.getElementById('see-gka-checkbox')
                console.log(checkbox.checked)
                if (checkbox.checked) {
                    $('#SeeGkaToggleOnModal').modal('show')
                } else {
                    $('#SeeGkaToggleOffModal').modal('show')
                }
            } else {
                $('#CannotToggleSeeGkaModal').modal('show')
            }
        })

    }())
</script>
<?php $this->end() ?>
<?= $this->App->viewEndComment() ?>

<?= $this->App->viewStartComment() ?>
<section class="panel payment service-disabled">
    <div class="panel-container">
        <span class="fa fa-lock service-disabled-icon"></span>
        <h3><?= __("Your team no longer has access to Goalous.") ?></h3>
        <?php if ($isTeamAdmin): ?>
            <h3><?= __('If you want to resume normal usage, please subscribe to our payment plan.') ?></h3>
            <a href="/payments" class="btn btn-primary service-subscribe"><?= __('Subscribe'); ?></a>
        <?php else: ?>
            <h3><?= __('If you want to resume normal usage, please contact to your team administrators.') ?></h3>
        <?php endif; ?>
        <p class="switchTeam-description"><?= __("Switch team") ?></p>
        <form class="">
            <?php echo $this->Form->input('current_team',
                array(
                    'type'      => 'select',
                    'options'   => !empty($my_teams) ? $my_teams : [
                        __('There is no team.')
                    ],
                    'value'     => $this->Session->read('current_team_id'),
                    'id'        => '',
                    'label'     => false,
                    'div'       => false,
                    'class'     => 'js-switchTeam switchTeam-select form-control disable-change-warning',
                    'wrapInput' => false,
                ))
            ?>
        </form>

    </div>
</section>
<?= $this->App->ViewEndComment() ?>

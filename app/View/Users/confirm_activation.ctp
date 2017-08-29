<?= $this->App->viewStartComment() ?>
<section class="panel subscription-status">
    <div class="panel-container">
        <h2>User Activation Confirmation</h2>
        <p>By activating the user, <?= h($user['User']['display_username']) ?>, the following changes will occur:</p>
        <div class="team-price-info">
            <h5 class="team-price-info-headline"><?= __('Monthly') ?></h5>
            <span class="team-price-info-number"><?= $amountPerUser ?><div class="team-price-info-detail">/<?= __('member'); ?>/<?= __('month'); ?></div></span>
        </div>
        <div class="team-price-info">
            <h5 class="team-price-info-headline"><?= __('Active Members') ?></h5>
            <i class="team-price-info-icon fa fa-user"></i>
            <span class="team-price-info-number"><?= $chargeMemberCount ?></span>
        </div>
        <div class="team-price-info">
            <h5 class="team-price-info-headline"><?= __('Estimated Total') ?></h5>
            <i class="team-price-info-icon fa fa-credit-card"></i>
            <span class="team-price-info-number"><?= $subTotal?><div
                    class="team-price-info-detail">/<?= __('month'); ?></div></span>
        </div>
    </div>
</section>

<?= $this->App->viewEndComment() ?>
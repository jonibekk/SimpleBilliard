<?= $this->App->viewStartComment() ?>
<section class="panel subscription-status">
    <div class="panel-container">
        <h3 class="subscription-status-headline"><?= __("Subscription Status") ?></h3>
        <h4 class="status-text">
            <?php if($serviceUseStatus==0){ // Free Trial ?>
                <i class="fa fa-unlock-alt free-trial"></i>
                <?= __('Free Trial') ?>
            <?php }elseif($serviceUseStatus==1){ // Subscribed ?>
                <i class="fa fa-check subscribed"></i>
                <?= __('Subscribed') ?>
            <?php }elseif($serviceUseStatus==2){ // Read Only ?>
                <i class="fa fa-lock read-only"></i>
                <?= __('Read Only') ?>
            <?php }else{ //Deactivated ?>
                <i class="fa fa-lock deactivated"></i>
                <?= __('Deactivated') ?>
            <?php } ?>
        </h4>
        <?php if($serviceUseStatus==0){ // Free Trial ?>
                <p class="subscription-status-detail"><?= __('Your team is currently using Goalous as a free trial. Your free trial will end on September 20, 2017'); ?></p>
            <?php }elseif($serviceUseStatus==1){ // Subscribed  ?>
                <p class="subscription-status-detail"><?= __('Your team has full access to Goalous.<br /><br />Go achieve your goal!'); ?></p>
            <?php }elseif($serviceUseStatus==2){ // Read Only ?>
                <p class="subscription-status-detail"><?= __('Your team can no longer create or edit content on Goalous. Your team account will be deactived on November 20, 2017'); ?></p>
            <?php }else{ //Deactivated ?>
                <p class="subscription-status-detail"><?= __('Your team no longer has access to  Goalous. Your team account will be deleted on December 20, 2017'); ?></p>
            <?php } ?>
        <a href="#" class="subscribe-btn btn btn-primary"><?= __('Subscribe') ?></a>
        <div class="team-price-info">
            <h5 class="team-price-info-headline"><?= __('Number of Members')?></h5>
            <i class="team-price-info-icon fa fa-user"></i>
            <span class="team-price-info-number"><?= $teamMemberCount ?></span>
        </div>
        <div class="team-price-info">
            <h5 class="team-price-info-headline"><?= __('Total Price')?></h5>
            <i class="team-price-info-icon fa fa-credit-card"></i>
            <span class="team-price-info-number">$19,990.00</span>
        </div>
    </div>
</section>
<?= $this->App->ViewEndComment() ?>
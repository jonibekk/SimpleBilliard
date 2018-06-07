<?= $this->App->viewStartComment() ?>
<?php if ($serviceUseStatus == Goalous\Enum\Team\ServiceUseStatus::PAID): ?>
<div class="sub-navigation">
    <h4 class="sub-nav-headline"><?= __("Billing") ?></h4>
        <ul>
            <li class="<?= $this->params['action'] == 'index' ? 'current' : '';?>">
                <?php if($this->params['action'] == 'index'){ ?>
                    <?=  __("Status") ?>
                <?php }else{ ?>
                    <a href="/payments/"><?=  __("Status") ?></a>
                <?php } ?>
            </li>
                <li class="<?= $this->params['action'] == 'history' ? 'current' : '';?>">
                    <?php if($this->params['action'] == 'history'){ ?>
                        <?=  __("History") ?>
                    <?php }else{ ?>
                        <a href="/payments/history"><?= __("History") ?></a>
                    <?php } ?>
                </li>
                <li class="<?= $this->params['action'] == 'method' ? 'current' : '';?>">
                    <?php if($this->params['action'] == 'method'){ ?>
                        <?=  __("Payment Method") ?>
                    <?php }else{ ?>
                        <a href="/payments/method"><?= __("Payment Method") ?></a>
                    <?php } ?>
                </li>
                <li class="<?= $this->params['action'] == 'contact_settings' ? 'current' : '';?>">
                    
                    <?php if($this->params['action'] == 'contact_settings'){ ?>
                        <?=  __("Contact Settings") ?>
                    <?php }else{ ?>
                        <a href="/payments/contact_settings"><?= __("Contact Settings") ?></a>
                    <?php } ?>
                </li>
                <span class="sub-nav-toggle fa fa-angle-down"></span>
        </ul>
    </div>
<?php endif; ?>
<?= $this->App->viewEndComment() ?>
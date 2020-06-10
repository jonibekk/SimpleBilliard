<?= $this->App->viewStartComment() ?>
<div class="glHeaderMobile">
    <div class="glHeaderMobile-left">
        <?php if($this->request->url === ''):?>
            <a href="/goals/kr_progress" class="nav-back-btn">
                <i class="material-icons">trending_up</i>
            </a>
        <?php else: ?>
            <?= $this->element('Header/back_btn'); ?>
        <?php endif; ?>
    </div>
    <div class="glHeaderMobile-right">
        <ul class="glHeaderMobile-nav">
            <li class="glHeaderMobile-nav-menu">
                <a id="GlHeaderMenuDropdown-Create" href="#" class="glHeaderMobile-nav-menu-link" data-toggle="dropdown">
                    <i class="material-icons">add_circle</i>
                </a>
                <ul class="dropdown-menu glHeader-nav-dropdown mod-mobile"
                    aria-labelledby="GlHeaderMenuDropdown-Create">
                    <?php if ($this->Session->read('current_team_id')): ?>
                        <li class="glHeader-nav-dropdown-menu">
                            <a class="glHeader-nav-dropdown-menu-link"
                               href="<?= $this->Html->url(['controller' => 'goals', 'action' => 'create', 'step1']) ?>">
                                <div class="glHeader-nav-dropdown-menu-link-left">
                                    <i class="material-icons">flag</i>
                                </div>
                                <p class=""><?= __('Create a goal') ?></p>
                            </a>
                        </li>
                        <li class="glHeader-nav-dropdown-menu">
                            <a class="glHeader-nav-dropdown-menu-link" href="/circles/create">
                                <div class="glHeader-nav-dropdown-menu-link-left">
                                    <i class="material-icons">group_work</i>
                                </div>
                                <p class=""><?= __('Create a circle') ?></p>
                            </a>
                        </li>
                    <?php endif; ?>
                    <?php if (IS_DEMO != true): ?>
                        <li class="glHeader-nav-dropdown-menu">
                            <a class="glHeader-nav-dropdown-menu-link"
                            href="<?= $this->Html->url(['controller' => 'teams', 'action' => 'add']) ?>">
                                <div class="glHeader-nav-dropdown-menu-link-left">
                                    <i class="material-icons">people</i>
                                </div>
                                <p class=""><?= __('Create a team') ?></p>
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>

            </li>
            <li class="glHeaderMobile-nav-menu">
                <a href="/search" class="glHeaderMobile-nav-menu-link">
                    <i class="material-icons">search</i>
                </a>
            </li>
        </ul>
    </div>
</div>
<?= $this->App->viewEndComment() ?>

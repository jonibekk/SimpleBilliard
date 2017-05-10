<?php
/**
 * @var $angular
 * @var $active
 */
$angular = isset($angular) ? $angular : false;
$active = isset($active) ? $active : '';
?>
<ul class="nav" style="font-size: 13px;">
    <li class=""><a class="<?php if ($active == 'index'): ?>active<?php endif ?>"
                    href="<?= $this->Html->url(['controller' => 'teams', 'action' => 'index']) ?>"><i
                class="fa fa-info-circle"></i> <?= __('Basic info') ?></a></li>

    <li class=""><a <?php if ($angular): ?>ui-sref="member"
                    <?php else: ?>href="<?= $this->Html->url([
                        'controller' => 'teams',
                        'action'     => 'main',
                        '#'          => '/'
                    ]) ?>"<?php endif ?>><i
                class="fa fa-user"></i> <?= __('Team Members') ?></a></li>
    <li class=""><a <?php if ($angular): ?>ui-sref="vision({team_id:team_id})"
                    <?php else: ?>href="<?= $this->Html->url([
                        'controller' => 'teams',
                        'action'     => 'main',
                        '#'          => '/vision/' . $this->Session->read('current_team_id')
                    ]) ?>" <?php endif ?>><i
                class="fa fa-rocket"></i> <?= __('Team Visions') ?></a></li>
    <li class=""><a <?php if ($angular): ?>ui-sref="group_vision({team_id:team_id})"
                    <?php else: ?>href="<?= $this->Html->url([
                        'controller' => 'teams',
                        'action'     => 'main',
                        '#'          => '/group_vision/' . $this->Session->read('current_team_id')
                    ]) ?>"<?php endif ?>><i
                class="fa fa-plane"></i> <?= __('Group Visions') ?></a></li>
    <li class=""><a class="<?php if ($active == 'insight'): ?>active<?php endif ?>"
                    href="<?= $this->Html->url(['controller' => 'teams', 'action' => 'insight']) ?>"><i
                class="fa fa-line-chart"></i> <?= __('Insights') ?></a></li>
    <li class=""><a class="<?php if ($active == 'insight_ranking'): ?>active<?php endif ?>"
                    href="<?= $this->Html->url(['controller' => 'teams', 'action' => 'insight_ranking']) ?>"><i
                class="fa fa-trophy"></i> <?= __('Rankings') ?></a></li>
    <li class=""><a class="<?php if ($active == 'insight_circle'): ?>active<?php endif ?>"
                    href="<?= $this->Html->url(['controller' => 'teams', 'action' => 'insight_circle']) ?>"><i
                class="fa fa-circle-o"></i> <?= __('Usage status of Circles') ?></a></li>
</ul>

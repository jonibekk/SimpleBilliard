<?
/**
 * @var CodeCompletionView $this
 * @var                    $unapproved_cnt
 * @var                    $done_cnt
 * @var                    $goal_info
 * @var                    $value_unit_list
 */
?>
<!-- START app/View/GoalApproval/done.ctp -->
<style type="text/css">
	.approval_body_text {
		font-size: 14px
	}

	.sp-feed-alt-sub {
		background: #f5f5f5;
		position: fixed;
		top: 50px;
		z-index: 1;
		box-shadow: 0 2px 4px rgba(0, 0, 0, .15);
		width: 100%;
		left: 0;
	}

	.approval_body_start_area {
		margin-top: 40px;
	}
</style>

<div class="col col-md-12 sp-feed-alt-sub" style="top: 50px;" id="SubHeaderMenu">
	<div class="col col-xxs-6 text-align_r">
		<a class="font_lightGray-veryDark no-line plr_18px sp-feed-link inline-block pt_12px height_40px"
		   id="SubHeaderMenuFeed"
		   href="<?= $this->Html->url(['controller' => 'goal_approval', 'action' => 'index']) ?>">
			<?= __d('gl', "処理待ち") ?> <? if ($unapproved_cnt > 0) {
				echo $unapproved_cnt;
			} ?></a>
	</div>
	<div class="col col-xxs-6">
		<a class="font_lightGray-veryDark no-line plr_18px sp-feed-link inline-block pt_12px height_40px sp-feed-active"
		   id="SubHeaderMenuGoal">
			<?= __d('gl', "処理済み") ?> <? if ($done_cnt > 0) {
				echo $done_cnt;
			} ?></a>
	</div>
</div>

<div class="approval_body_start_area">
	<div class="row">
		<div class="col-sm-8 col-sm-offset-2">
			<? if (isset($goal_info) === true && count($goal_info) > 0) { ?>
				<? foreach ($goal_info as $goal) { ?>
					<div class="panel panel-default" id="AddGoalFormPurposeWrap">
						<div class="panel-body goal-set-heading clearfix">

							<p class="approval_body_text">
								<? if ($goal['Collaborator']['valued_flg'] === '1') { ?>

							<p class="text-right"><?= __d('gl', "評価対象") ?></p>
							<? } else {
								if ($goal['Collaborator']['valued_flg'] === '2') { ?>
									<p class="text-right"><?= __d('gl', "評価対象ではない") ?></p>
								<? }
							} ?>
							</p>

							<p class="approval_body_text"><?= __d('gl', "名前") ?>
								: <?= $goal['User']['display_username']; ?></p>

                            <p class="approval_body_text"><?= __d('gl', "カテゴリ") ?>
                                : <?= h($goal['Goal']['GoalCategory']['name']); ?></p>

							<p class="approval_body_text"><?= __d('gl', "ゴール名") ?>: <?= h($goal['Goal']['name']); ?></p>

							<p class="approval_body_text"><?= $goal['Collaborator']['type'] === '1' ?
									__d('gl', "リーダー") : __d('gl', "コラボレーター"); ?></p>

							<p class="approval_body_text"><?= __d('gl', "役割") ?>
								: <?= h($goal['Collaborator']['role']); ?></p>

							<p class="approval_body_text"><?= __d('gl', "単位") ?>
								: <?= $value_unit_list[$goal['Goal']['value_unit']]; ?></p>

							<p class="approval_body_text"><?= __d('gl', "達成時") ?>
								: <?= (double)$goal['Goal']['target_value']; ?></p>

							<p class="approval_body_text"><?= __d('gl', "開始時") ?>
								: <?= (double)$goal['Goal']['start_value']; ?></p>

							<p class="approval_body_text"><?= __d('gl', "期限日") ?>
								: <?= $this->TimeEx->date(h($goal['Goal']['end_date'])) ?></p>

							<p class="approval_body_text"><?= __d('gl', "重要度") ?>
								: <?= $goal['Collaborator']['priority']; ?></p>

							<p class="approval_body_text"><?= __d('gl', "目的") ?>
								: <?= h($goal['Goal']['Purpose']['name']); ?></p>

							<p class="approval_body_text"><?= __d('gl', "詳細") ?>
								: <?= $this->TextEx->autoLink($goal['Goal']['description']); ?></p>

							<?=
							$this->Html->image('ajax-loader.gif',
											   [
												   'class'         => 'lazy',
												   'data-original' => $this->Upload->uploadUrl($goal,
																							   "Goal.photo",
																							   ['style' => 'medium']),
												   'width'         => '48px',
												   'error-img'     => "/img/no-image-link.png",
											   ]
							)
							?>
						</div>

						<? if (isset($goal['msg']) === false) { ?>
							<div class="panel-footer addteam_pannel-footer goalset_pannel-footer">
								<div class="row">
									<div class="pull-right">
										<? if ($goal['Collaborator']['valued_flg'] === '1') { ?>
											<a href="/goal_approval/wait/<?= $goal['Collaborator']['id']; ?>"
											   class="btn btn-primary"><?= __d('gl', "評価対象としない") ?></a>
										<? }
										else {
											if ($goal['Collaborator']['valued_flg'] === '2') { ?>
												<a href="/goal_approval/approval/<?= $goal['Collaborator']['id']; ?>"
												   class="btn btn-primary"><?= __d('gl', "評価対象とする") ?></a>
											<? }
										} ?>
									</div>
								</div>
							</div>
						<? } ?>

					</div>
				<? } ?>
			<? } ?>
		</div>
	</div>
</div>
<!-- END app/View/GoalApproval/done.ctp -->
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
		   id="SubHeaderMenuFeed" href="/goalapproval">
			処理待ち </a>
	</div>
	<div class="col col-xxs-6">
		<a class="font_lightGray-veryDark no-line plr_18px sp-feed-link inline-block pt_12px height_40px sp-feed-active"
		   id="SubHeaderMenuGoal">
			処理済み </a>
	</div>
</div>

<div class="approval_body_start_area">
	<div class="row">
		<div class="col-sm-8 col-sm-offset-2">
			<? if (isset($goal_info) === true && count($goal_info) > 0) { ?>
				<? foreach ($goal_info as $goal) { ?>
					<div class="panel panel-default" id="AddGoalFormPurposeWrap">
						<div class="panel-heading goal-set-heading clearfix">
							<p class="approval_body_text">名前: <? echo $goal['User']['local_username']; ?></p>

							<p class="approval_body_text"><? echo $goal['Goal']['goal_category_id'] === '3' ? '職務' : '成長'; ?></p>

							<p class="approval_body_text">ゴール名: <? echo $goal['Goal']['name']; ?></p>

							<p class="approval_body_text"><? echo $goal['Collaborator']['type'] === '1' ? 'リーダー' : 'コラボレーター'; ?></p>

							<p class="approval_body_text">単位, 達成時, 開始時</p>

							<p class="approval_body_text">期限日: <? echo $goal['Goal']['end_date']; ?></p>

							<p class="approval_body_text">重要度: <? echo $goal['Collaborator']['priority']; ?></p>

							<p class="approval_body_text">目的: <? echo $goal['Goal']['Purpose']['name']; ?></p>

							<p class="approval_body_text">詳細: <? echo $goal['Goal']['name']; ?></p>

							<p class="approval_body_text">ゴールイメージ: <? echo $goal['Goal']['photo_file_name']; ?></p>
						</div>
					</div>
				<? } ?>
			<? } ?>
		</div>
	</div>
</div>

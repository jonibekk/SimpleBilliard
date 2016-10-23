<div class="panel panel-default">
    <!-- search by keyword -->
    <div class="panel-block bd-b-sc4">
        <div class="goal-search-keyword mb_10px">
            <input type="text" class="goal-search-keyword-input" placeholder="キーワードで検索">
            <span class="goal-search-keyword-submit fa fa-search"></span>
        </div>
        <div class="text-align_r">
            <a href="#">絞り込み</a>
        </div>

    </div>
    <!-- filter -->
    <div class="panel-block bd-b-sc4 ">
        <div class="gl-form-horizontal">
            <label class="gl-form-horizontal-col gl-form-label" for="">評価期間</label>
            <div class="gl-form-horizontal-col">
                <select name="term" class="form-control gl">
                    <option value="">今期</option>
                    <option value="">来期</option>
                    <option value="">前期</option>
                    <option value="">もっと前</option>
                </select>
            </div>
        </div>
        <div class="gl-form-horizontal">
            <label class="gl-form-horizontal-col gl-form-label" for="">カテゴリ</label>
            <div class="gl-form-horizontal-col">
                <select name="category" class="form-control gl">
                    <option value="">すべて</option>
                    <option value="">職務</option>
                    <option value="">成長</option>
                </select>
            </div>
        </div>
        <div class="gl-form-horizontal">
            <label class="gl-form-horizontal-col gl-form-label" for="">達成/未達成</label>
            <div class="gl-form-horizontal-col">
                <select name="status" class="form-control gl">
                    <option value="">すべて</option>
                    <option value="">達成</option>
                    <option value="">成長</option>
                </select>
            </div>
        </div>
        <div class="gl-form">
            <label class="gl-form-label" for="">ラベル</label>
            <p class="gl-form-guide">Enterを押すと追加されます。(最大5個まで登録可能)</p>
            <input type="text" class="form-control gl">
        </div>
        <div class="text-align_c p_4px">
            <a href="#">閉じる <span class="fa fa-angle-up"></span></a>
        </div>
    </div>
    <!-- search result count and order -->
    <div class="panel-block">
        <div class="row">
            <div class="pull-left">検索結果100件</div>
            <div class="pull-right">
                <!--                <p class="goal-search-order-text">新着順 <span class="fa fa-angle-down"></span></p>-->
                <div role="group">
                    <p class="dropdown-toggle goal-search-order-text" data-toggle="dropdown" role="button" aria-expanded="false">
                        <span class="">新着順</span>
                        <i class="fa fa-angle-down"></i>
                    </p>
                    <ul class="dropdown-menu pull-right" role="menu">
                        <li><a href="/goals/index/order:new">新着順</a></li>
                        <li><a href="/goals/index/order:action">アクションが多い順</a></li>
                        <li><a href="/goals/index/order:result">出した成果が多い順</a></li>
                        <li><a href="/goals/index/order:follow">フォロワーが多い順</a></li>
                        <li><a href="/goals/index/order:collabo">コラボが多い順</a></li>
                        <li><a href="/goals/index/order:progress">進捗率が高い順</a></li>
                    </ul>
                </div>
            </div>
        </div>

    </div>
    <!-- goal list -->
    <div class="panel-block bd-b-sc4">
        <div class="row">
            <div class="col-xxs-12">
                <div class="col-xxs-3 col-xs-2">
                    <a href="/goals/ajax_get_goal_description_modal/goal_id:879" class="modal-ajax-get">
                        <img src="/img/no-image-goal.jpg" class="lazy img-rounded"
                             style="width: 48px; height: 48px; display: inline;" data-original="/img/no-image-goal.jpg"
                             alt=""></a>
                </div>
                <div class="col-xxs-9 col-xs-10">
                    <div class="col-xxs-12 goals-page-card-title-wrapper">
                        <a href="#" class="goals-page-card-title">
                            <p class="goals-page-card-title-text">
                                <span>Goalous WAU500</span>
                            </p>
                        </a>
                    </div>
                    <ul class="gl-labels mb_8px">
                        <li class="gl-labels-item">AWS</li>
                        <li class="gl-labels-item">Goalous</li>
                        <li class="gl-labels-item">リーダーシップ</li>
                    </ul>
                    <p class="font_lightgray font_12px">リーダー: Jobs Steve</p>
                    <dl class="gl-goal-info-counts">
                        <dt class="gl-goal-info-counts-title"><i class="fa fa-check-circle"></i></dt>
                        <dd class="gl-goal-info-counts-description">10</dd>
                        <dt class="gl-goal-info-counts-title"><i class="fa fa-key"></i></dt>
                        <dd class="gl-goal-info-counts-description">10</dd>
                        <dt class="gl-goal-info-counts-title"><i class="fa fa-heart"></i></dt>
                        <dd class="gl-goal-info-counts-description">10</dd>
                        <dt class="gl-goal-info-counts-title"><i class="fa fa-child"></i></dt>
                        <dd class="gl-goal-info-counts-description">10</dd>
                    </dl>
                    <div class="col-xxs-12 ptb_8px">
                        <div class="col-xxs-6 col-xs-4">
                            <a class="btn btn-white-radius"
                               href="#" data-class="toggle-follow" goal-id="1343">
                                <i class="fa fa-heart font_rougeOrange  mr_4px"></i>
                                <span class="">フォロー</span>
                            </a>
                        </div>
                        <div class="col-xxs-6 col-xs-4">
                            <a class="btn btn-white-radius"
                               data-toggle="modal" data-target="#ModalCollabo_1343"
                               href="/goals/ajax_get_collabo_change_modal/goal_id:1343">
                                <i class="fa fa-child font_rougeOrange font_18px mr_4px" style=""></i>
                                <span class="">コラボる</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="panel-block bd-b-sc4">
        <div class="row">
            <!-- START /vagrant_data/app/View/Elements/Goal/index_items.ctp -->
            <div class="col-xxs-12">
                <div class="col-xxs-3 col-xs-2">
                    <a href="/goals/ajax_get_goal_description_modal/goal_id:879" class="modal-ajax-get">
                        <img src="/img/no-image-goal.jpg" class="lazy img-rounded"
                             style="width: 48px; height: 48px; display: inline;" data-original="/img/no-image-goal.jpg"
                             alt=""></a>
                </div>
                <div class="col-xxs-9 col-xs-10">
                    <div class="col-xxs-12 goals-page-card-title-wrapper">
                        <a href="#" class="goals-page-card-title">
                            <p class="goals-page-card-title-text">
                                <span>弥生プロジェクト全体にて2017年3末までに累計XXXX万円の営業利益を生み出す（精査中）</span>
                            </p>
                        </a>
                    </div>
                    <ul class="gl-labels mb_8px">
                        <li class="gl-labels-item">AWS</li>
                        <li class="gl-labels-item">Goalous</li>
                        <li class="gl-labels-item">リーダーシップ</li>
                    </ul>
                    <p class="font_lightgray font_12px">リーダー: Jobs Steve</p>
                    <dl class="gl-goal-info-counts">
                        <dt class="gl-goal-info-counts-title"><i class="fa fa-check-circle"></i></dt>
                        <dd class="gl-goal-info-counts-description">10</dd>
                        <dt class="gl-goal-info-counts-title"><i class="fa fa-key"></i></dt>
                        <dd class="gl-goal-info-counts-description">10</dd>
                        <dt class="gl-goal-info-counts-title"><i class="fa fa-heart"></i></dt>
                        <dd class="gl-goal-info-counts-description">10</dd>
                        <dt class="gl-goal-info-counts-title"><i class="fa fa-child"></i></dt>
                        <dd class="gl-goal-info-counts-description">10</dd>
                    </dl>
                    <div class="col-xxs-12 ptb_8px">
                        <div class="col-xxs-6 col-xs-4">
                            <a class="btn btn-white-radius"
                               href="#" data-class="toggle-follow" goal-id="1343">
                                <i class="fa fa-heart font_rougeOrange  mr_4px"></i>
                                <span class="">フォロー</span>
                            </a>
                        </div>
                        <div class="col-xxs-6 col-xs-4">
                            <a class="btn btn-white-radius"
                               data-toggle="modal" data-target="#ModalCollabo_1343"
                               href="/goals/ajax_get_collabo_change_modal/goal_id:1343">
                                <i class="fa fa-child font_rougeOrange font_18px mr_4px" style=""></i>
                                <span class="">コラボる</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

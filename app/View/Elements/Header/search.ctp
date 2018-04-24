<?= $this->App->viewStartComment() ?>
<div class="header-search-toggle">
    <form id="NavSearchFormToggle" class="nav-form-group nav-search-form-group panel panel-default" role="search" autocomplete="off">
        <div class="searchBoxMain">
            <span class="deleteicon">
                <i id="NavSearchIconToggle" class="fa fa-search search-header-icon"></i>
                <input type="text"
                       id="NavSearchInputToggle"
                       maxlength="<?= SELECT2_QUERY_LIMIT ?>"
                       class="searchBox-input-main disable-change-warning <?= $is_mb_app || $isMobileBrowser ? "mb-hide-keyboard-on-enter" : "" ?>"
                       placeholder='<?= __("Search Members, Goals, Circles") ?>'>
                <i id="NavSearchInputClearToggle" class="fa fa-times search-header-icon"></i>
            </span>
            <div id="NavSearchHideToggle" class="topicSearchList-header-cancel-main">
                <a class="topicSearchList-header-cancel-button-main"><?= __("Cancel") ?></a>
            </div>
        </div>
        <div id="NavSearchResultsToggle" class="nav-search-result redux-infinite-scroll"></div>
    </form>
</div>
<div class="header-search">
    <form id="NavSearchForm" class="nav-form-group nav-search-form-group panel panel-default" role="search" autocomplete="off">
        <div class="searchBoxMain">
            <span class="deleteicon">
                <i id="NavSearchIcon" class="fa fa-search search-header-icon"></i>
                <input type="text"
                       id="NavSearchInput"
                       maxlength="<?= SELECT2_QUERY_LIMIT ?>"
                       class="searchBox-input-main disable-change-warning"
                       placeholder='<?= __("Search Members, Goals, Circles") ?>'>
                <i id="NavSearchInputClear" class="fa fa-times search-header-icon"></i>
            </span>
            <div id="NavSearchHide" class="topicSearchList-header-cancel-main">
                <a class="topicSearchList-header-cancel-button-main"><?= __("Cancel") ?></a>
            </div>
        </div>
        <div id="NavSearchResults" class="nav-search-result redux-infinite-scroll"></div>
    </form>
</div>
<div class="header-white-bg"></div>
<?= $this->App->viewEndComment() ?>
import React from "react";
import ReactDOM from "react-dom";
import * as KeyCode from "~/common/constants/KeyCode";
import GoalSearchFilter from "~/goal_search/components/elements/GoalSearchFilter";
import GoalCard from "~/goal_search/components/elements/GoalCard";
import NoGoal from "~/goal_search/components/elements/NoGoal";
import Loading from "~/goal_search/components/elements/Loading";
import InfiniteScroll from "redux-infinite-scroll";

export default class Goals extends React.Component {
  constructor(props) {
    super(props)
    this.updateFilter = this.updateFilter.bind(this);
  }

  componentWillMount() {
    this.props.fetchInitialData()
  }

  componentWillReceiveProps(nextProps) {
  }

  showFilter() {
    this.props.updateData({show_filter: !this.props.goal_search.show_filter})
  }

  updateFilter(e, key, val) {
    e.preventDefault()
    this.props.updateFilter({[key]: val})
  }


  searchByKeyword(e) {
    e && e.preventDefault()
    this.props.updateFilter({keyword: ReactDOM.findDOMNode(this.refs.keyword).value.trim()})
  }

  updateKeyword(e) {
    e && e.preventDefault()
    this.props.updateKeyword({keyword: e.target.value})
  }

  downloadCsv(e) {
    e.preventDefault();
    this.props.downloadCsv();
  }

  fetchMoreGoals() {
    const {search_result} = this.props.goal_search
    const url = search_result.paging.next
    if (!url) {
      return
    }
    this.props.fetchMoreGoals(url)

  }

  renderGoals(goals) {
    let uid = this.makeRandomStr()
    return goals.map((goal) => {
      return (
        <GoalCard goal={goal} key={`${uid}-${goal.id}`}/>
      )
    })
  }

  makeRandomStr(length = 8) {
    // 生成する文字列に含める文字セット
    const c = "abcdefghijklmnopqrstuvwxyz0123456789";

    const cl = c.length;
    let r = "";
    for (let i = 0; i < length; i++) {
      r += c[Math.floor(Math.random() * cl)];
    }
    return r
  }

  render() {
    const props = this.props.goal_search

    const goals = props.search_result.data
    const order = props.search_conditions["order"] ? props.search_conditions["order"] : "new";

    const search_orders = {
      new: __("Creation Date"),
      action: __("Actions number"),
      result: __("Key results number"),
      follow: __("Followers number"),
      collab: __("Collaborators number"),
      progress: __("Progress rate")
    }

    // 検索結果エリア
    let search_result_el;
    let exist_goal = false;
    if (props.loading == true) {
      search_result_el = <Loading />
    } else if (props.search_result.count > 0) {
      search_result_el = (
        <InfiniteScroll
          loadMore={this.fetchMoreGoals.bind(this)}
          loader={<Loading />}
          loadingMore={props.loading_more}
          items={this.renderGoals(goals)}
          elementIsScrollable={false}
        />
      )
      exist_goal = true
    } else {
      search_result_el = <NoGoal />
    }

    let csvDownloadLink = <div></div>;
    if (cake.is_current_team_admin) {
      if (props.downloading_csv) {
        csvDownloadLink  = <div><img src="/img/lightbox/loading.gif" width="16" height="16"/><span className="ml_2px"> {__("Downloading CSV...")}</span></div>;
      } else {
        csvDownloadLink  = <a href="#" onClick={(e) => this.downloadCsv(e)}>{__("Download CSV")}</a>;
      }
    }

    return (
      <div className="panel panel-default">
        {/* search by keyword */}
        <div className="panel-block bd-b-sc4">
          <form onSubmit={this.searchByKeyword.bind(this)}>
            <div className="goal-search-keyword mb_10px">
              <input type="text" className="goal-search-keyword-input" placeholder={__("Search by goal name")}
                     ref="keyword"
                     maxLength="50"
                     value={props.search_conditions.keyword || ""}
                     onChange={(e) => this.updateKeyword(e)}
              />
              <span onClick={this.searchByKeyword.bind(this)} className="goal-search-keyword-submit fa fa-search"/>
            </div>
          </form>
          <div className="goal-search-header-links">
            {csvDownloadLink}
            <a href="#" onClick={this.showFilter.bind(this)}>{__("Filter")}</a>
          </div>
        </div>

        {/* search filter */}
        <GoalSearchFilter
          show_filter={props.show_filter}
          suggestions={props.suggestions}
          label_keyword={props.label_keyword}
          categories={props.categories}
          labels={props.labels}
          search_conditions={props.search_conditions}
          input_labels={props.search_conditions.labels}
        />


        {/* search result count and order */}
        <div className={`panel-block ${exist_goal ? "" : "hide"}`}>
          <div className="row">
            <div className="pull-left">{__("Search result")} {props.search_result.count}{__(" count")}</div>
            <div className="pull-right">
              <div role="group">
                <p className="dropdown-toggle goal-search-order-text" data-toggle="dropdown" role="button"
                   aria-expanded="false">
                  <span className>{search_orders[order]}</span>
                  <i className="fa fa-angle-down ml_2px"/>
                </p>
                <ul className="dropdown-menu pull-right" role="menu">
                  {(() => {
                    let search_orders_el = []
                    const search_orders_keys = Object.keys(search_orders)
                    for (let i = 0; i < search_orders_keys.length; i++) {
                      let key = search_orders_keys[i]
                      search_orders_el.push(
                        <li key={key}>
                          <a href="#"
                             onClick={(e) => this.updateFilter(e, "order", key)}>
                            {search_orders[key]}
                          </a>
                        </li>
                      )
                    }
                    return search_orders_el
                  })()}
                </ul>
              </div>
            </div>
          </div>
        </div>

        {search_result_el}
      </div>
    )
  }
}

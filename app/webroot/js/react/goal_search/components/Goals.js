import React from "react";
import ReactDOM from "react-dom";
import * as KeyCode from "~/common/constants/KeyCode";
import GoalSearchFilter from "~/goal_search/components/elements/GoalSearchFilter";
import GoalCard from "~/goal_search/components/elements/GoalCard";
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

  onSubmit(e) {
    e.preventDefault()
    if (e.keyCode == KeyCode.ENTER) {
      return false
    }
    this.props.validateGoal(this.props.params.goalId, this.getInputDomData())
  }

  showFilter() {
    this.props.updateData({showFilter: !this.props.goal_search.showFilter})
  }

  updateFilter(e, key, val) {
    e.preventDefault()
    this.props.updateFilter({[key]: val})
  }

  searchByKeyword(e) {
    e && e.preventDefault()
    this.props.updateFilter({keyword: ReactDOM.findDOMNode(this.refs.keyword).value.trim()})
  }


  fetchMoreGoals() {
    const {search_result} = this.props.goal_search
    const url = search_result.paging.next
    this.props.fetchMoreGoals(url)

  }

  renderGoals(goals) {
    return goals.map((goal) => {
      return (
        <GoalCard goal={goal} key={goal.id}/>
      )
    })
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
      collabo: __("Collaborators number"),
      progress: __("Progress rate")
    }

    return (
      <div className="panel panel-default">
        {/* search by keyword */}
        <div className="panel-block bd-b-sc4">
          <form onSubmit={this.searchByKeyword.bind(this)}>
            <div className="goal-search-keyword mb_10px">
              <input type="text" className="goal-search-keyword-input" placeholder="キーワードで検索" ref="keyword"
                     maxLength="50"/>
              <span onClick={this.searchByKeyword.bind(this)} className="goal-search-keyword-submit fa fa-search"/>
            </div>
          </form>
          <div className="text-align_r">
            <a href="#" onClick={this.showFilter.bind(this)}>絞り込み</a>
          </div>
        </div>

        {/* search filter */}
        <GoalSearchFilter
          showFilter={props.showFilter}
          suggestions={props.suggestions}
          label_keyword={props.label_keyword}
          categories={props.categories}
          labels={props.labels}
          search_conditions={props.search_conditions}
          input_labels={props.search_conditions.labels}
        />


        {/* search result count and order */}
        <div className="panel-block">
          <div className="row">
            <div className="pull-left">検索結果 {props.search_result.count}件</div>
            <div className="pull-right">
              <div role="group">
                <p className="dropdown-toggle goal-search-order-text" data-toggle="dropdown" role="button"
                   aria-expanded="false">
                  <span className>{search_orders[order]}</span>
                  <i className="fa fa-angle-down"/>
                </p>
                <ul className="dropdown-menu pull-right" role="menu">
                  {(() => {
                    let search_orders_el = []
                    for(let key of Object.keys(search_orders)) {
                      search_orders_el.push(
                        <li key={key}>
                          <a href="#" search-order={key}
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

        {/*goal list*/}
        <InfiniteScroll
          loadMore={this.fetchMoreGoals.bind(this)}
          items={this.renderGoals(goals)}
          elementIsScrollable={false}
        />

      </div>
    )
  }
}

Goals.propTypes = {
  // goal_search: React.PropTypes.object.isRequired,
}

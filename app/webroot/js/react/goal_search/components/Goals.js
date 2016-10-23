import React from "react";
import ReactDOM from "react-dom";
import * as KeyCode from "~/common/constants/KeyCode";
import GoalSearchFilter from "~/goal_search/components/elements/GoalSearchFilter";
import GoalCard from "~/goal_search/components/elements/GoalCard";

export default class Goals extends React.Component {
  constructor(props) {
    super(props)

    this.onChange = this.onChange.bind(this)
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

  onKeyPress(e) {
    // ラベル入力でEnterキーを押した場合submitさせない
    // e.keyCodeはonKeyPressイベントでは取れないのでe.charCodeを使用
    if (e.charCode == KeyCode.ENTER) {
      e.preventDefault()
      return false
    }
  }

  onChange(e, childKey = "") {
    this.props.updateInputData({[e.target.name]: e.target.value}, childKey)
  }

  showFilter() {
    this.props.updateData({showFilter: !this.props.goal_search.showFilter})
  }

  updateFilter(key, val) {
    this.props.updateFilter({[key]: val})
  }

  searchByKeyword(e) {
    e && e.preventDefault()
    this.props.updateFilter({keyword: ReactDOM.findDOMNode(this.refs.keyword).value.trim()})
  }

  render() {
    const props = this.props.goal_search

    return (
      <div className="panel panel-default">
        {/* search by keyword */}
        <div className="panel-block bd-b-sc4">
          <form onSubmit={this.searchByKeyword.bind(this)}>
            <div className="goal-search-keyword mb_10px">
              <input type="text" className="goal-search-keyword-input" placeholder="キーワードで検索" ref="keyword" maxLength="50"/>
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
                  <span className>新着順</span>
                  <i className="fa fa-angle-down"/>
                </p>
                <ul className="dropdown-menu pull-right" role="menu">
                  <li><a href="#" search-order="new">新着順</a></li>
                  <li><a href="#" search-order="action">アクションが多い順</a></li>
                  <li><a href="#" search-order="result">出した成果が多い順</a></li>
                  <li><a href="#" search-order="follow">フォロワーが多い順</a></li>
                  <li><a href="#" search-order="collabo">コラボが多い順</a></li>
                  <li><a href="#" search-order="progress">進捗率が高い順</a></li>
                </ul>
              </div>
            </div>
          </div>
        </div>

        {/*goal list*/}
        {props.search_result.data.map((goal) => {
          return <GoalCard goal={goal} key={goal.id}/>
        })}
      </div>
    )
  }
}

Goals.propTypes = {
  // goal_search: React.PropTypes.object.isRequired,
}

import React from "react";
import Kr from '~/kr_column/components/Kr'
import Loading from "~/kr_column/components/Loading";
import InfiniteScroll from "redux-infinite-scroll";

export default class Krs extends React.Component {
  constructor(props) {
    super(props)
    this.state = {
      selected_goal: __('All Goals')
    }
    this.updateGoalFilter = this.updateGoalFilter.bind(this);
  }

  updateGoalFilter(e, index = null) {
    const goalName = index !== null ? this.props.goals[index]['name'] : __('All Goals')
    const goalId = index !== null ? this.props.goals[index]['id'] : null
    this.setState({ selected_goal: goalName })
    this.props.fetchKrsFilteredGoal(goalId)
  }

  render() {
    const {krs, kr_count, goals, loading_krs} = this.props
    if (goals.length === 0) {
      return null
    }

    const render_krs = krs.map((kr, i) => {
      return (
        <Kr key_result={kr.key_result}
            action_results={kr.action_results}
            goal={kr.goal}
            key={i}
        />
      )
    })

    return (
      <div className="panel panel-default dashboard-krs">
        <div className="dashboard-krs-header">
          <div className="title">KRs { kr_count ? `(${kr_count})` : '' }</div>
          <div role="group" className="pull-right goal-filter oneline-ellipsis">
            <div className="dropdown-toggle" data-toggle="dropdown" role="button"
               aria-expanded="false">
              <div className="selected-goal oneline-ellipsis">
                <span>{ this.state.selected_goal }</span>
              </div>
            </div>
            <ul className="dropdown-menu pull-right" role="menu">
              <li>
                <a href="#"
                   onClick={(e) => this.updateGoalFilter(e)}>
                  { __('All Goals') }
                </a>
              </li>
              {(() => {
                let goal_elems = []
                for (let i = 0; i < goals.length; i++) {
                  const goalId = goals[i]['id']
                  goal_elems.push(
                    <li key={goalId}>
                      <a href="#"
                         onClick={(e) => this.updateGoalFilter(e, i)}
                         className="block oneline-ellipsis">
                        <span>{goals[i]['name']}</span>
                      </a>
                    </li>
                  )
                }
                return goal_elems
              })()}
            </ul>
          </div>
          <div className="dropdown-opener dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
            <i className="fa fa-angle-down ml_2px"/>
          </div>
        </div>
        <ul className="dashboard-krs-columns">
          <InfiniteScroll
            loadMore={ this.props.fetchMoreKrs.bind(this) }
            loadingMore={ loading_krs }
            items={ render_krs }
            elementIsScrollable={ false }
            loader=""
          />
        </ul>
        { loading_krs && <Loading /> }
      </div>
    )
  }
}

Krs.propTypes = {
  krs: React.PropTypes.array,
  goals: React.PropTypes.array,
  kr_count: React.PropTypes.number,
  loading_krs: React.PropTypes.bool
};
Krs.defaultProps = { krs: [], goals: [], kr_count: 0, loading_krs: false };

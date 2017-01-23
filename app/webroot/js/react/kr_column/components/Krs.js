import React from "react";
import Kr from '~/kr_column/components/Kr'
import Loading from "~/kr_column/components/Loading";

export default class Krs extends React.Component {
  constructor(props) {
    super(props)
    this.state = {
      selected_goal: __('All Goals')
    }
    this.updateGoalFilter = this.updateGoalFilter.bind(this);
  }

  updateGoalFilter(e, goalId = null) {
    const goalName = goalId ? this.props.goals[goalId] : __('All Goals')
    this.setState({ selected_goal: goalName})
    this.props.fetchKrsFilteredGoal(goalId)
  }

  render() {
    const {krs, kr_count, goals} = this.props
    if (goals.length === 0) {
      return null
    }

    return (
      <div className="panel panel-default dashboard-krs">
        <div className="dashboard-krs-header">
          <div className="title">KRs { kr_count ? `(${kr_count})` : '' }</div>
          <div role="group" className="pull-right goal-filter">
            <div className="dropdown-toggle" data-toggle="dropdown" role="button"
               aria-expanded="false">
              <div className="selected-goal oneline-ellipsis">
                <span>{ this.state.selected_goal }</span>
              </div>
              <span className="dropdown-opener"><i className="fa fa-angle-down ml_2px"/></span>
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
                const goal_keys = Object.keys(goals)
                for (let i = 0; i < goal_keys.length; i++) {
                  let goalId = goal_keys[i]
                  goal_elems.push(
                    <li key={goalId}>
                      <a href="#"
                         onClick={(e) => this.updateGoalFilter(e, goalId)}
                         className="block oneline-ellipsis">
                        <span>{goals[goalId]}</span>
                      </a>
                    </li>
                  )
                }
                return goal_elems
              })()}
            </ul>
          </div>
        </div>
        <ul className="dashboard-krs-columns">
          { krs.map((kr, i) => {
            const {key_result, action_results, goal} = kr
            return (
              <Kr key_result={key_result}
                  action_results={action_results}
                  goal={ goal }
                  key={i}
              />
            )
          }) }
        </ul>
        { this.props.loading_krs && <Loading /> }
      </div>
    )
  }
}

Krs.propTypes = {
  krs: React.PropTypes.array,
  goals: React.PropTypes.object,
  kr_count: React.PropTypes.number,
  loading_krs: React.PropTypes.bool
};
Krs.defaultProps = { krs: [], goals: {}, kr_count: 0, loading_krs: false };

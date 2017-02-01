import React from 'react'
import GoalCard from "~/goal_approval/components/elements/detail/GoalCard";

export default class GoalBlock extends React.Component {
  constructor(props) {
    super(props)

    this.state = { displayed_previous: false }
    this.displayPrevious = this.displayPrevious.bind(this)
  }

  displayPrevious() {
    this.setState({ displayed_previous: true })
  }

  render() {
    const goal = this.props.goal
    const is_leader = this.props.is_leader
    const displayed_previous = this.state.displayed_previous
    const existsChangeLogs = goal.goal_change_log || goal.kr_change_log
    const view_previous_button = () => {
      if (!displayed_previous && existsChangeLogs) {
        return (
          <div className="goals-approval-detail-view-previous">
              <a className="goals-approval-detail-view-more-comments" onClick={ this.displayPrevious }>
                <i className="fa fa-angle-down" aria-hidden="true"></i>
                <span className="goals-approval-interactive-link"> { __('View Previous') } </span>
              </a>
          </div>
        )
      } else {
        return null
      }
    }
    const previous_goal_card = () => {
      if (displayed_previous && existsChangeLogs) {
        return (
          <GoalCard goal={ goal.goal_change_log || goal }
                    goal_category={ goal.goal_change_log.goal_category || goal.goal_category }
                    top_key_result={ goal.kr_change_log || goal.top_key_result } />
        )
      } else {
        return null
      }
    }

    return (
      <div className={ `goals-approval-detail-goal ${is_leader && 'mod-bgglay'}` }>
          <GoalCard goal={ { id: goal.id, name: goal.name, small_img_url: goal.small_img_url, modified: goal.modified } }
                    goal_category={ goal.goal_category }
                    top_key_result={ goal.top_key_result } />
          { displayed_previous && <p className="goals-approval-detail-goal-previous-info">{ __('Previous goal') }</p> }
          { view_previous_button() }
          { previous_goal_card() }
      </div>
    )
  }
}

GoalBlock.propTypes = {
  goal: React.PropTypes.object,
  is_leader: React.PropTypes.bool
}
GoalBlock.defaultProps = { goal: {}, is_leader: true };

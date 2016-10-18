import React from 'react'
import { GoalCard } from "~/goal_approval/components/elements/detail/GoalCard";

export class GoalBlock extends React.Component {
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
    const view_previous_button = () => {
      return (
        <div className="goals-approval-detail-view-previous">
            <a className="goals-approval-detail-view-more-comments" onClick={ this.props.displayPrevious }>
              <i className="fa fa-angle-down" aria-hidden="true"></i>
              <span className="goals-approval-interactive-link"> { __('View Previous') } </span>
            </a>
        </div>
      )
    }
    const current_goal_card = () => {
      return (
        <GoalCard displayPrevious={ this.displayPrevious }
                  goal={ { id: goal.id, name: goal.name, small_img_url: goal.small_img_url, modified: goal.modified } }
                  category={ goal.category }
                  top_key_result={ goal.top_key_result }
                  is_current={ true }
                  displayed_previous={ displayed_previous } />
      )
    }
    const previous_goal_card = () => {
      return (
        <GoalCard goal={ goal.goal_change_log }
                  category={goal.category}
                  top_key_result={goal.tkr_change_log}
                  is_current={ false } />
      )
    }

    return (
      <div className={ `goals-approval-detail-goal ${is_leader && 'mod-bgglay'}` }>
        { current_goal_card() }
        { !displayed_previous && <p className="goals-approval-detail-previous-info">{ __('Previous goal') }</p> }
        { !displayed_previous && view_previous_button() }
        { goal.goal_change_log && displayed_previous && previous_goal_card() }
      </div>
    )
  }
}

GoalBlock.propTypes = {
  goal: React.PropTypes.object,
  is_leader: React.PropTypes.bool
}
GoalBlock.defaultProps = { goal: {}, is_leader: true };

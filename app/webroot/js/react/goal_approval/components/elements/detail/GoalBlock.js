import React from 'react'
import PresentGoalCard from "~/goal_approval/components/elements/detail/PresentGoalCard";
import PrevisouGoalCard from "~/goal_approval/components/elements/detail/PreviousGoalCard";

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
    const { goal, goal_change_log, goal_changed_columns, top_key_result, tkr_change_log, tkr_changed_columns, is_leader } = this.props
    const existsChangeLogs = goal_change_log && tkr_change_log
    const displayed_previous = this.state.displayed_previous

    return (
      <div className={ `goals-approval-detail-goal ${is_leader && 'mod-bgglay'}` }>

          {/* 現在のゴール */}
          <PresentGoalCard goal={ goal }
                           goal_changed_columns={ goal_changed_columns }
                           top_key_result={ top_key_result }
                           tkr_changed_columns={ tkr_changed_columns } />

          {/* 「Previsou goal」ラベル */}
          { displayed_previous &&
            <p className="goals-approval-detail-goal-previous-info">{ __('Previous goal') }</p>
          }

          {/* 「View Previous」ボタン */}
          { !displayed_previous && existsChangeLogs &&
            <div className="goals-approval-detail-view-previous">
                <a className="goals-approval-detail-view-more-comments" onClick={ this.displayPrevious }>
                  <i className="fa fa-angle-down" aria-hidden="true"></i>
                  <span className="goals-approval-interactive-link"> { __('View Previous') } </span>
                </a>
            </div>
          }

          {/* 変更前のゴール */}
          { displayed_previous && existsChangeLogs &&
            <PrevisouGoalCard goal={ goal_change_log }
                              top_key_result={ tkr_change_log } />
          }
      </div>
    )
  }
}

GoalBlock.propTypes = {
  goal: React.PropTypes.object,
  is_leader: React.PropTypes.bool
}
GoalBlock.defaultProps = { goal: {}, is_leader: true };

import React from 'react'
import { CoachCard } from './elements/list/coach_card'
import { CoacheeCard } from './elements/list/coachee_card'
import { ViewMoreButton } from './elements/list/view_more_button'

export default class ListComponent extends React.Component {
  componentWillMount() {
    const is_initialize = true

    this.props.fetchGoalApprovals(is_initialize)
  }

  render() {
    return (
      <section className="panel panel-default col-sm-8 col-sm-offset-2 clearfix goals-approval">
          <h1 className="goals-approval-heading">Goal approval list <span>(2)</span></h1>
          <ul>
            { this.props.list.goal_approvals.map( (goal_approval) => {
              if(goal_approval.is_coach) {
                return <CoachCard goal_approval={ goal_approval } key={goal_approval.name} />;
              } else {
                return <CoacheeCard goal_approval={ goal_approval } key={goal_approval.name}  />;
              }
            }) }
          </ul>
          {/* TODO: fetchGoalApprovalsを即時間数で囲わないとなぜかコールした際の引数 がtrueになる。要調査。 */}
          { !this.props.list.done_loading_all_data ? <ViewMoreButton handleOnClick={ () => this.props.fetchGoalApprovals() }
                                                                     is_loading={ this.props.list.fetching_goal_approvals } /> : null }
      </section>
    )
  }
}
ListComponent.propTypes = {
  list: React.PropTypes.object.isRequired,
  fetchGoalApprovals: React.PropTypes.func.isRequired
}

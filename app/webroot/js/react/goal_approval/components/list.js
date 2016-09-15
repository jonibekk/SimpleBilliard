import React from 'react'
import { CoachCard } from './elements/list_coach_card'
import { CoacheeCard } from './elements/list_coachee_card'
import { ListMoreViewButton } from './elements/list_more_view_button'

export default class ListComponent extends React.Component {
  componentWillMount() {
    const is_initialize = true

    this.props.fetchGaolApprovals(is_initialize)
  }

  render() {
    return (
      <section className="panel panel-default col-sm-8 col-sm-offset-2 clearfix goals-approval">
          <h1 className="goals-approval-heading">Goal approval list <span>(2)</span></h1>
          <ul>
            { this.props.goal_approval.goal_approvals.map((goal_approval) => {
              if(goal_approval.is_coach) {
                return <CoachCard goal_approval={ goal_approval } key={goal_approval.name} />;
              } else {
                return <CoacheeCard goal_approval={ goal_approval } key={goal_approval.name}  />;
              }
            }) }
          </ul>
          {(() => {
            if(!this.props.goal_approval.done_loading_all_data) {
              return <ListMoreViewButton handleOnClick={ () => this.props.fetchGaolApprovals() } is_loading={ this.props.goal_approval.fetching_goal_approvals} />
            }
          })()}
      </section>
    )
  }
}

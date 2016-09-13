import React from 'react'
import { CoachCard } from './elements/coach_card'
import { CoacheeCard } from './elements/coachee_card'

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
      </section>
    )
  }
}

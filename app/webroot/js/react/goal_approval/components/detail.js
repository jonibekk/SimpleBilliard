import React from 'react'
import { Comments } from './elements/detail_comments'
import { GoalCard } from './elements/detail_goal_card'
import { UserCard } from './elements/detail_user_card'
import { ApproveSubmitArea } from './elements/detail_submit_area'

export default class DetailComponent extends React.Component {

  componentWillMount() {
    this.props.fetchGaolApproval(this.props.params.goal_id)
  }

  render() {
    if(!this.props.detail.goal_approval.collaborator) {
      return (
        <div></div>
      )
    }
    return (
      <section className="panel panel-default col-sm-8 col-sm-offset-2 clearfix goals-approval">
          <h1 className="goals-approval-heading">Confirm this goal</h1>
          <div className="goals-approval-detail">
              <UserCard collaborator={ this.props.detail.goal_approval.collaborator } leader={ this.props.detail.goal_approval.leader } />
              <GoalCard goal={ this.props.detail.goal_approval } />
              <Comments comments={ this.props.detail.goal_approval.comments } />
              <h1 className="goals-approval-heading">Check it</h1>
              <ApproveSubmitArea />
          </div>
      </section>
    )
  }
}

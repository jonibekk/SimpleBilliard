import React from 'react'
import { Comments } from './elements/detail_comments'
import { GoalCard } from './elements/detail_goal_card'
import { UserCard } from './elements/detail_user_card'
import { ApproveSubmitArea } from './elements/detail_submit_area'

export default class DetailComponent extends React.Component {

  componentWillMount() {
    this.props.fetchGoalApproval(this.props)
  }

  render() {
    return (
      <section className="panel panel-default col-sm-8 col-sm-offset-2 clearfix goals-approval">
          <h1 className="goals-approval-heading">Confirm this goal</h1>
          <UserCard user={ this.props.detail.goal_approval.collaborator } />
          <GoalCard goal={ this.props.detail } />
          <div className="goals-approval-detail-comments">
              <h2>comments</h2>
              <Comments comments={ this.props.detail.comments } />
          </div>
          <ApproveSubmitArea />
      </section>
    )
  }
}

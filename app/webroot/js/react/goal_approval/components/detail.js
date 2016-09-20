import React from 'react'
import { Comments } from './elements/detail/comments'
import { GoalCard } from './elements/detail/goal_card'
import { UserCard } from './elements/detail/user_card'
import { CoachFooter } from './elements/detail/coach_footer'
import { CoacheeFooter } from './elements/detail/coachee_footer'

export default class DetailComponent extends React.Component {
  constructor(props) {
    super(props)
  }

  componentWillMount() {
    this.props.fetchGaolApproval(this.props.params.goal_id)
  }

  render() {
    return (
      <section className="panel panel-default col-sm-8 col-sm-offset-2 clearfix goals-approval">
          <h1 className="goals-approval-heading">Confirm this goal</h1>
          <div className="goals-approval-detail">
              <UserCard collaborator={ this.props.detail.goal_approval } key="user card" />
              <GoalCard collaborator={ this.props.detail.goal_approval } key="goal card" />
              <Comments collaborator={ this.props.detail.goal_approval } key="comments" />
              <h1 className="goals-approval-heading">Check it</h1>
              {(() => {
                if(this.props.detail.goal_approval.is_mine) {
                  return <CoacheeFooter />;
                } else {
                  return <CoachFooter handlePostSetAsTarget={ post_data => this.props.postSetAsTarget(post_data) }
                                      handlePostRemoveFromTarget={ post_data => this.props.postRemoveFromTarget(post_data) } />;
                }
              })()}
          </div>
      </section>
    )
  }
}

DetailComponent.propTypes = {
  detail: React.PropTypes.object.isRequired,
  fetchGaolApproval: React.PropTypes.func.isRequired,
  postSetAsTarget: React.PropTypes.func.isRequired,
  postRemoveFromTarget: React.PropTypes.func.isRequired
}

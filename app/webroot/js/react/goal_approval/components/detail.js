import React from 'react'
import { Comments } from './elements/detail_comments'
import { GoalCard } from './elements/detail_goal_card'
import { UserCard } from './elements/detail_user_card'
import { CoachFooter } from './elements/detail_coach_footer'
import { CoacheeFooter } from './elements/detail_coachee_footer'

export default class DetailComponent extends React.Component {

  componentWillMount() {
    this.props.fetchGaolApproval(this.props.params.goal_id)
  }

  render() {
    return (
      <section className="panel panel-default col-sm-8 col-sm-offset-2 clearfix goals-approval">
          <h1 className="goals-approval-heading">Confirm this goal</h1>
          {(() => { if(Object.keys(this.props.detail.goal_approval).length > 0) return (
              <div className="goals-approval-detail">
                  <GoalCard goal={ this.props.detail.goal_approval.goal } is_leader={ this.props.detail.goal_approval.is_leader } />
                  <Comments approval_histories={ this.props.detail.goal_approval.approval_histories } />
                  <h1 className="goals-approval-heading">Check it</h1>
                  {(() => {
                    if(this.props.detail.goal_approval.is_mine) {
                      return <CoacheeFooter />;
                    } else {
                      return <CoachFooter />;
                    }
                  })()}
              </div>
          )})()}
      </section>
    )
  }
}

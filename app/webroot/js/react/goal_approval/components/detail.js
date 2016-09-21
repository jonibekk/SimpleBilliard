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
    this.props.initDetailPage()
    this.props.fetchGaolApproval(this.props.params.goal_id)
  }

  componentWillReceiveProps(nextProps) {
    if (nextProps.detail.to_list_page) {
      // browserHistory.push('/goals/approval/list')
      document.location.href = '/goals/approval/list'
    }
  }

  render() {
    const detail = this.props.detail

    return (
      <section className="panel panel-default col-sm-8 col-sm-offset-2 clearfix goals-approval">
          <h1 className="goals-approval-heading">Confirm this goal</h1>
          <div className="goals-approval-detail">
              <UserCard collaborator={ detail.goal_approval } key="user card" />
              <GoalCard goal={ detail.goal_approval.goal }
                        is_leader={ detail.is_leader } key="goal card" />
              <Comments collaborator={ detail.goal_approval } />
              <h1 className="goals-approval-heading">Check it</h1>
              {(() => {
                if(detail.goal_approval.is_mine) {
                  return <CoacheeFooter validationErrors={ detail.validationErrors }
                                        is_leader={ detail.is_leader } />;
                } else {
                  return <CoachFooter validationErrors={ detail.validationErrors }
                                      posting_set_as_target= { detail.posting_set_as_target }
                                      posting_remove_from_target= { detail.posting_remove_from_target }
                                      handlePostSetAsTarget={ input_data => this.props.postSetAsTarget(input_data) }
                                      handlePostRemoveFromTarget={ input_data => this.props.postRemoveFromTarget(input_data) } />;
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
  postRemoveFromTarget: React.PropTypes.func.isRequired,
  initDetailPage: React.PropTypes.func.isRequired
}

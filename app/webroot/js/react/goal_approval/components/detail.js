import React from 'react'
import { Comments } from "~/common/components/approval/Comments";
import { GoalCard } from "~/common/components/approval/GoalCard";
import { UserCard } from "~/common/components/approval/UserCard";
import { CoachFooter } from "~/common/components/approval/CoachFooter";
import { CoacheeFooter } from "~/common/components/approval/CoacheeFooter";

export default class DetailComponent extends React.Component {
  constructor(props) {
    super(props)
  }

  componentWillMount() {
    this.props.initDetailPage()
    this.props.fetchCollaborator(this.props.params.collaborator_id)
  }

  componentWillReceiveProps(nextProps) {
    if (nextProps.detail.to_list_page) {
      // browserHistory.push('/goals/approval/list')
      document.location.href = '/goals/approval/list'
    }
  }

  render() {
    if(Object.keys(this.props.detail.collaborator).length == 0) {
      return null
    }

    const detail = this.props.detail
    const page_title = detail.collaborator.is_mine ? __("Goal details") : __("Set as a target for evaluation?")
    const coachee_footer = (() => {
      return <CoacheeFooter validationErrors={ detail.validationErrors }
                            is_leader={ detail.collaborator.is_leader }
                            goal_id={ detail.collaborator.goal.id } />;
    })()
    const coach_footer = (() => {
      return <CoachFooter validationErrors={ detail.validationErrors }
                          posting_set_as_target={ detail.posting_set_as_target }
                          posting_remove_from_target={ detail.posting_remove_from_target }
                          collaborator_id={this.props.params.collaborator_id}
                          handlePostSetAsTarget={ input_data => this.props.postSetAsTarget(input_data) }
                          handlePostRemoveFromTarget={ input_data => this.props.postRemoveFromTarget(input_data) } />;
    })()

    return (
      <section className="panel panel-default col-sm-8 col-sm-offset-2 clearfix goals-approval">
          <h1 className="goals-approval-heading">{ page_title }</h1>
          <div className="goals-approval-detail">
              <UserCard collaborator={ detail.collaborator } />
              <GoalCard goal={ detail.collaborator.goal }
                        is_leader={ detail.collaborator.is_leader } />
              <Comments approvalHistories={ detail.collaborator.approval_histories } />
              {/* footer */}
              { detail.collaborator.is_mine ? coachee_footer : coach_footer }
          </div>
      </section>
    )
  }
}

DetailComponent.propTypes = {
  detail: React.PropTypes.object.isRequired,
  fetchCollaborator: React.PropTypes.func.isRequired,
  postSetAsTarget: React.PropTypes.func.isRequired,
  postRemoveFromTarget: React.PropTypes.func.isRequired,
  initDetailPage: React.PropTypes.func.isRequired
}

import React from 'react'
import { Comments } from "~/common/components/approval/Comments";
import { GoalBlock } from "~/common/components/approval/GoalBlock";
import { UserCard } from "~/common/components/approval/UserCard";
import { CoachFooter } from "~/common/components/approval/CoachFooter";
import { CoacheeFooter } from "~/common/components/approval/CoacheeFooter";

export default class DetailComponent extends React.Component {
  constructor(props) {
    super(props)
  }

  componentWillMount() {
    this.props.initDetailPage()
    this.props.fetchGoalMember(this.props.params.goal_member_id)
  }

  componentWillReceiveProps(nextProps) {
    if (nextProps.detail.to_list_page) {
      // browserHistory.push('/goals/approval/list')
      document.location.href = '/goals/approval/list'
    }
  }

  render() {
    if (Object.keys(this.props.detail.goal_member).length == 0) {
      return null
    }

    const detail = this.props.detail
    const page_title = detail.goal_member.is_mine ? __("Goal details") : __("Set as a target for evaluation?")
    const coachee_footer = (() => {
      return <CoacheeFooter validationErrors={ detail.validationErrors }
                            goal_member={ detail.goal_member }
                            goal_id={ detail.goal_member.goal.id }
                            current_url={this.props.location.pathname}
                            handleClickWithdraw={ () => this.props.postWithdraw(this.props.params.goal_member_id) }/>;
    })()
    const coach_footer = (() => {
      return <CoachFooter validationErrors={ detail.validationErrors }
                          posting_set_as_target={ detail.posting_set_as_target }
                          posting_remove_from_target={ detail.posting_remove_from_target }
                          goal_member_id={this.props.params.goal_member_id}
                          handlePostSetAsTarget={ input_data => this.props.postSetAsTarget(input_data) }
                          handlePostRemoveFromTarget={ input_data => this.props.postRemoveFromTarget(input_data) }/>;
    })()

    return (
      <section className="panel panel-default col-sm-8 col-sm-offset-2 clearfix goals-approval">
          <h1 className="goals-approval-heading">{ page_title }</h1>
          <div className="goals-approval-detail">
              <UserCard collaborator={ detail.goal_member } />
              <GoalBlock goal={ detail.goal_member.goal }
                         is_leader={ detail.goal_member.is_leader } />
              <Comments approval_histories={ detail.goal_member.approval_histories }
                        view_more_text={ detail.goal_member.histories_view_more_text} />
              {/* footer */}
              { detail.goal_member.is_mine ? coachee_footer : coach_footer }
          </div>
      </section>
    )
  }
}

DetailComponent.propTypes = {
  detail: React.PropTypes.object.isRequired,
  fetchGoalMember: React.PropTypes.func.isRequired,
  postSetAsTarget: React.PropTypes.func.isRequired,
  postRemoveFromTarget: React.PropTypes.func.isRequired,
  initDetailPage: React.PropTypes.func.isRequired,
  postWithdraw: React.PropTypes.func.isRequired
}

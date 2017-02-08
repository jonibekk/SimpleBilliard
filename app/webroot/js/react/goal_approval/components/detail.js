import React from 'react'
import Comments from "~/goal_approval/components/elements/detail/Comments";
import GoalBlock from "~/goal_approval/components/elements/detail/GoalBlock";
import UserCard from "~/goal_approval/components/elements/detail/UserCard";
import CoachFooter from "~/goal_approval/components/elements/detail/CoachFooter";
import CoacheeFooter from "~/goal_approval/components/elements/detail/CoacheeFooter";
import {isObject} from "~/util/base.js";


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
      document.location.href = '/goals/approval/list'
    }

    if (nextProps.detail.to_top_page) {
      document.location.href = '/'
    }
  }

  render() {
    if (Object.keys(this.props.detail.goal_member).length == 0) {
      return null
    }

    const detail = this.props.detail
    const goal_change_log = isObject(detail.goal_member.goal_change_log) ? detail.goal_member.goal_change_log : {};
    const tkr_change_log = isObject(detail.goal_member.tkr_change_log) ? detail.goal_member.tkr_change_log : {};

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
    const coach_action = detail.goal_member.latest_coach_action_statement

    return (
      <section className="panel panel-default col-sm-8 col-sm-offset-2 clearfix goals-approval">
          <h1 className="goals-approval-heading">{ page_title }</h1>
          <div className="goals-approval-detail">
              { coach_action && <div className="goals-approval-detail-coach-action">{ coach_action }</div> }
              <UserCard goal_member={ detail.goal_member } />
              <GoalBlock goal={ detail.goal_member.goal }
                         goal_change_log={ goal_change_log }
                         goal_changed_columns={ detail.goal_member.goal_changed_columns || {} }
                         top_key_result={ detail.goal_member.top_key_result }
                         tkr_change_log={ tkr_change_log }
                         tkr_changed_columns={ detail.goal_member.tkr_changed_columns || {} }
                         is_leader={ detail.goal_member.is_leader } />
              <Comments approval_histories={ detail.goal_member.approval_histories }
                        view_more_text={ detail.goal_member.histories_view_more_text }
                        is_mine={ detail.is_mine }
                        posting={ detail.posting_comment }
                        goal_member_id={ this.props.params.goal_member_id }
                        add_comments={ detail.add_comments }
                        comment={ detail.comment } />
              {/* footer */}
              { detail.goal_member.is_mine ? coachee_footer : coach_footer }
          </div>
      </section>
    )
  }
}

DetailComponent.propTypes = {
  detail: React.PropTypes.object.isRequired,
  add_comments: React.PropTypes.array.isRequired,
  fetchGoalMember: React.PropTypes.func.isRequired,
  postSetAsTarget: React.PropTypes.func.isRequired,
  postRemoveFromTarget: React.PropTypes.func.isRequired,
  initDetailPage: React.PropTypes.func.isRequired,
  postWithdraw: React.PropTypes.func.isRequired
}

DetailComponent.defaultProps = { detail: {}, add_comments: [] }

import React from 'react'
import ReactDOM from "react-dom"
import {Link} from 'react-router'
import {TopKeyResult} from '~/common/constants/Model'
import InvalidMessageBox from "~/common/components/InvalidMessageBox";

export default class CoachFooter extends React.Component {
  constructor(props) {
    super(props);

    this.state = {
      select_clear_status: null,
      select_important_status: null
    }
  }

  handleClickClear() {
    this.setState({select_clear_status: TopKeyResult.IS_CLEAR})
  }

  handleClickNotClear() {
    this.setState({select_clear_status: TopKeyResult.IS_NOT_CLEAR})
  }

  handleClickImportant() {
    this.setState({select_important_status: TopKeyResult.IS_IMPORTANT})
  }

  handleClickNotImportant() {
    this.setState({select_important_status: TopKeyResult.IS_NOT_IMPORTANT})
  }

  handleSubmit(e) {
    e.preventDefault()
  }

  handleSubmitSetAsTarget() {
    const post_data = {
      approval_history: {
        comment: ReactDOM.findDOMNode(this.refs.comment).value.trim()
      },
      goal_member: {
        id: this.props.goal_member_id
      }
    }

    this.props.handlePostSetAsTarget(post_data)
  }

  handleSubmitRemoveFromTarget() {
    const post_data = {
      approval_history: {
        comment: ReactDOM.findDOMNode(this.refs.comment).value.trim(),
        select_clear_status: this.state.select_clear_status,
        select_important_status: this.state.select_important_status
      },
      goal_member: {
        id: this.props.goal_member_id
      }
    }

    this.props.handlePostRemoveFromTarget(post_data)
  }

  render() {
    const can_submit_set_as_target = this.state.select_clear_status === TopKeyResult.IS_CLEAR && this.state.select_important_status === TopKeyResult.IS_IMPORTANT
    const can_submit_remove_from_target = this.state.select_clear_status !== null && this.state.select_important_status !== null
    const validation_errors = this.props.validationErrors
    const loading_image = () => <img src="/img/ajax-loader.gif" className="signup-img-loader"/>;

    return (
      <div className="goals-approval-detail-choice">
        <form onSubmit={ this.handleSubmit.bind(this) }>
          <div className="goals-approval-detail-choice-block">
            <label className="goals-approval-input-label"
                   htmlFor="">{__("Do you think this Top Key Result is clear ?")}</label>
            <a
              className={`btn pull-left ${this.state.select_clear_status === TopKeyResult.IS_CLEAR ? 'goals-approval-btn-choiced' : 'goals-approval-btn-choice'}`}
              htmlFor="" onClick={ this.handleClickClear.bind(this) }>{__("Yes")}</a>
            <a
              className={`btn pull-right ${this.state.select_clear_status === TopKeyResult.IS_NOT_CLEAR ? 'goals-approval-btn-choiced' : 'goals-approval-btn-choice'}`}
              htmlFor="" onClick={ this.handleClickNotClear.bind(this) }>{__("No")}</a>
          </div>

          <div className="goals-approval-detail-choice-block mt_18px">
            <label className="goals-approval-input-label"
                   htmlFor="">{__("Do you think this Top Key Result is the most important to achieve the goal?")}</label>
            <a
              className={`btn pull-left ${this.state.select_important_status === TopKeyResult.IS_IMPORTANT ? 'goals-approval-btn-choiced' : 'goals-approval-btn-choice'}`}
              htmlFor="" onClick={ this.handleClickImportant.bind(this) }>{__("Yes")}</a>
            <a
              className={`btn pull-right ${this.state.select_important_status === TopKeyResult.IS_NOT_IMPORTANT ? 'goals-approval-btn-choiced' : 'goals-approval-btn-choice'}`}
              htmlFor="" onClick={ this.handleClickNotImportant.bind(this) }>{__("No")}</a>
          </div>

          <div className="goals-approval-detail-choice-block mt_18px">
            <label className="goals-approval-input-label" htmlFor="">{__("Add as a target for evaluation ?")}</label>
            <textarea className="form-control goals-approval-detail-input-comment-form" name="comment" ref="comment"
                      id="comment" cols="30" rows="2" placeholder={__("Add your comment (optional)")}></textarea>
            <InvalidMessageBox message={validation_errors.comment}/>
          </div>
          <div className="goals-approval-detail-bottom-choice">
            { this.props.posting_set_as_target ? loading_image() : '' }
            <button
              className={`btn pull-left ${can_submit_set_as_target ? 'goals-approval-btn-active' : 'goals-approval-btn-nonactive'}`}
              disabled={`${!can_submit_set_as_target ? "disabled" : ""}`}
              type="submit"
              onClick={ this.handleSubmitSetAsTarget.bind(this) }>{__("Yes")}</button>
            { this.props.posting_remove_from_target ? loading_image() : '' }
            <button
              className={`btn pull-right ${can_submit_remove_from_target ? 'goals-approval-btn-active' : 'goals-approval-btn-nonactive'}`}
              disabled={`${!can_submit_remove_from_target ? "disabled" : ""}`}
              type="submit"
              onClick={ this.handleSubmitRemoveFromTarget.bind(this) }>{__("No")}</button>
            <Link to="/goals/approval/list" className="btn goals-approval-btn-cancel">{__("Cancel")}</Link>
          </div>
        </form>
      </div>
    )
  }
}

CoachFooter.propTypes = {
  validationErrors: React.PropTypes.object.isRequired,
  posting_set_as_target: React.PropTypes.bool.isRequired,
  posting_remove_from_target: React.PropTypes.bool.isRequired,
  handlePostSetAsTarget: React.PropTypes.func.isRequired,
  handlePostRemoveFromTarget: React.PropTypes.func.isRequired
}

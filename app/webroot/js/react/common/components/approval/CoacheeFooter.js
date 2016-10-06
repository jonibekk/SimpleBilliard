import React from 'react'
import ReactDOM from 'react-dom'
import { Link } from 'react-router'
import { Collaborator } from "~/common/constants/Model";

export class CoacheeFooter extends React.Component {
  constructor(props) {
    super(props)
  }

  /**
   * 一時的にコレボレータの「 Edit Role」を従来のモーダルで動かす
   * TODO: Edit Role機能実装後この処理は行わないので削除する
   */
  componentDidMount() {
    const ApprovalStatus = Collaborator.ApprovalStatus

    if(status == ApprovalStatus.DONE && !this.props.collaborator.is_leader) {
      ReactDOM.findDOMNode(this.refs.collabo_link).setAttribute("data-toggle", "modal")
      ReactDOM.findDOMNode(this.refs.collabo_link).setAttribute("data-target", `#ModalCollabo_${this.props.goal_id}`)
    }
  }

  render() {
    const nextPageButton = (() => {
      const status = this.props.collaborator.approval_status
      const ApprovalStatus = Collaborator.ApprovalStatus

      // Withdraw
      if(status == ApprovalStatus.NEW || status == ApprovalStatus.REAPPLICATION) {
        return <a className="btn goals-approval-btn-fullsize-active"
                  onClick={ this.props.handleClickWithdraw }>{ __("Withdraw") }</a>
      }
      // Edit goal
      if(this.props.collaborator.is_leader) {
        return <a>aaa</a>
        return <a className="btn goals-approval-btn-fullsize-active"
                  onClick={ () => { document.location.href = `/goals/${this.props.goal_id}/edit` }}>{ __('Edit Goal') }</a>
      }
      // Edit role
      return <a href={`/goals/ajax_get_collabo_change_modal/goal_id:${this.props.goal_id}`}
                className="btn goals-approval-btn-fullsize-active modal-ajax-get-collabo"
                ref="collabo_link">{ __('Edit Role') }</a>
    })()

    return (
      <div className="goals-approval-detail-choice">
          { nextPageButton }
          <Link to="/goals/approval/list" className="btn goals-approval-btn-cancel">{ __("Back") }</Link>
      </div>
    )
  }
}

CoacheeFooter.propTypes = {
  collaborator: React.PropTypes.object,
  goal_id: React.PropTypes.oneOfType([
    React.PropTypes.string,
    React.PropTypes.number
  ])
}

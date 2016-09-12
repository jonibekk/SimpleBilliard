import React from 'react'
import { Link } from 'react-router'

export class CoacheeCard extends React.Component {
  render() {
    return (
      <li className={ this.props.goal_approval.collaborator.approval_status == 1 ? "goals-approval-list-item" : "goals-approval-list-item mod-coach-evaluating" }>
          <Link className="goals-approval-list-item-link" to={ `/goals/approval/detail/${this.props.goal_approval.id}` }>
              <img src={ this.props.goal_approval.user.photo_file_name } className="goals-approval-list-item-image" alt="" width="32" height="32" />
              <div className="goals-approval-list-item-info">
                  <p className="goals-approval-list-item-info-user-name">{ this.props.goal_approval.user.name }</p>
                  <p className="goals-approval-list-item-info-goal-name">{ this.props.goal_approval.name }</p>
                  <p className="goals-approval-list-item-info-goal-attr">{ this.props.goal_approval.collaborator.type == 1 ? __('Leader') : __('Collaborator') }・{ this.props.goal_approval.collaborator.approval_status == 1 ? __('Evaluated') : __('Not Evaluated') }</p>
              </div>
              <p className="goals-approval-list-item-detail"><i className="fa fa-angle-right" ariaHidden="true"></i></p>
          </Link>
      </li>
    )
  }
}

//propTypesは外で定義する
CoacheeCard.propTypes = {
  goal_approval: React.PropTypes.object.isRequired
}

// props初期値指定
// CoacheeCard.defaultProps = {name: "hoge"};

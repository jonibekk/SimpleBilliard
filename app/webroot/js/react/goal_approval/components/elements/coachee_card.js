import React from 'react'
import { Link } from 'react-router'

export class CoacheeCard extends React.Component {
  render() {
    let role = ''
    let status = ''
    let is_incomplete = false

    // Define role
    if(this.props.goal_approval.collaborator.type == 1) {
      role = __('Leader')
    } else {
      role = __('Collaborator')
    }
    // Define status
    if(this.props.goal_approval.collaborator.approval_status == 1) {
      status = __('Evaluated')
    } else if(this.props.goal_approval.collaborator.status_type !== null ) {
      status = __('Waiting')
      is_incomplete = true
    } else {
      status = __('Not Evaluated')
    }
    return (
      <li className="goals-approval-list-item">
          <Link className="goals-approval-list-item-link" to={ `/goals/approval/detail/${this.props.goal_approval.id}` }>
              <img src={ this.props.goal_approval.collaborator.user.photo_file_name } className="goals-approval-list-item-image" alt="" width="32" height="32" />
              <div className="goals-approval-list-item-info">
                  <p className="goals-approval-list-item-info-user-name">{ this.props.goal_approval.collaborator.user.name }</p>
                  <p className="goals-approval-list-item-info-goal-name">{ this.props.goal_approval.name }</p>
                  <p className="goals-approval-list-item-info-goal-attr">{ role }・<span className={ is_incomplete ? 'goals-approval-list-item-info-goal-attr-incomplete' : '' }>{ status }</span></p>
              </div>
              <p className="goals-approval-list-item-detail"><i className="fa fa-check font_33px" ariaHidden="true"></i></p>
          </Link>
      </li>
    )
  }
}

CoacheeCard.propTypes = {
  goal_approval: React.PropTypes.object.isRequired
}

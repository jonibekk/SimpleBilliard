import React from 'react'
import { Link } from 'react-router'

export class CoacheeCard extends React.Component {
  render() {
    const collaborator = this.props.collaborator
    const is_leader = collaborator.type == 1
    const is_done = collaborator.approval_status == 2;
    const is_target_evaluation = is_done && collaborator.is_target_evaluation == true
    const is_wating = !is_done && (collaborator.approval_status == 0 || collaborator.approval_status == 1)
    let role = ''
    let status = ''
    let is_incomplete = false

    // Define role
    if(is_leader) {
      role = __('Leader')
    } else {
      role = __('Collaborator')
    }
     // Define status
    if(is_target_evaluation) {
      status = __('Evaluated')
    } else if(is_wating) {
      status = __('Waiting')
      is_incomplete = true
    } else {
      status = __('Not Evaluated')
    }
    return (
      <li className="goals-approval-list-item">
          <div className={`goals-approval-list-item ${ is_incomplete ? "is-incomplete is-waiting" : "is-complete" }`}>
              <Link className="goals-approval-list-item-link" to={ `/goals/approval/detail/${collaborator.id}` }>
                  <img className="goals-approval-list-item-image" src={ collaborator.user.small_img_url } alt="" width="32" height="32" />
                  <div className="goals-approval-list-item-info">
                      <p className="goals-approval-list-item-info-user-name">{ collaborator.user.display_username }</p>
                      <p className="goals-approval-list-item-info-goal-name">{ collaborator.goal.name }</p>
                      <p className="goals-approval-list-item-info-goal-attr">{ role }・<span className="mod-status">{ status }</span></p>
                  </div>
                  <p className="goals-approval-list-item-detail">
                    <i className="fa fa-check" ariaHidden="true"></i>
                  </p>
              </Link>
          </div>
      </li>
    )
  }
}

CoacheeCard.propTypes = {
  collaborator: React.PropTypes.object.isRequired
}

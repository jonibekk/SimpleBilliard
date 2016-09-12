import React from 'react'
import { Link } from 'react-router'

export default class ListComponent extends React.Component {

  componentWillMount() {
    this.props.fetchGaolApprovals()
  }

  render() {
    return (
      <section className="panel panel-default col-sm-8 col-sm-offset-2 clearfix goals-approval">
          <h1 className="goals-approval-heading">Goal approval list <span>(2)</span></h1>
          <ul>
              { this.props.goal_approval.goal_approvals.map((goal) => { return (
                  <li className="goals-approval-list-item" key={ goal.id }>
                      <Link className="goals-approval-list-item-link" to={ `/goals/approval/detail/${goal.id}` }>
                          <img src={ goal.user.photo_file_name } className="goals-approval-list-item-image" alt="" width="32" height="32" />
                          <div className="goals-approval-list-item-info">
                              <p className="goals-approval-list-item-info-user-name">{ goal.user.name }</p>
                              <p className="goals-approval-list-item-info-goal-name">{ goal.name }</p>
                              <p className="goals-approval-list-item-info-goal-attr">{ goal.collaborator.type == 1 ? __('Leader') : __('Collaborator') }・{ goal.collaborator.approval_status == 1 ? __('Evaluated') : __('Not Evaluated') }</p>
                          </div>
                          <p className="goals-approval-list-item-detail"><i className="fa fa-angle-right" ariaHidden="true"></i>
                          </p>
                      </Link>
                  </li>
                )}) }
          </ul>
      </section>
    )
  }
}

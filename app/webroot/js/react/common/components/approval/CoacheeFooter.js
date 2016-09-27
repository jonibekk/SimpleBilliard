import React from 'react'
import { Link } from 'react-router'

export class CoacheeFooter extends React.Component {
  render() {
    const to_edit_link_text = this.props.is_leader ? __('Edit Goal') : __('Edit Role')

    return (
      <div className="goals-approval-detail-choice">
          <a onClick={ () => { document.location.href = `/goals/${this.props.goal_id}/edit` }} className="btn goals-approval-btn-fullsize-active">{ to_edit_link_text }</a>
          <Link to="/goals/approval/list" className="btn goals-approval-btn-cancel">Back</Link>
      </div>
    )
  }
}

CoacheeFooter.propTypes = {
  is_leader: React.PropTypes.bool,
  goal_id: React.PropTypes.integer
}

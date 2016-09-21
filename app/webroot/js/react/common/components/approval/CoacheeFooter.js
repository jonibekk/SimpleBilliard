import React from 'react'
import { Link } from 'react-router'

export class CoacheeFooter extends React.Component {
  render() {
    const to_edit_link_text = this.props.is_leader ? 'Edit Goal' : 'Edit Role'

    return (
      <div className="goals-approval-detail-choice">
          <Link to="" className="btn goals-approval-btn-fullsize-active">{ to_edit_link_text }</Link>
          <Link to="/goals/approval/list" className="btn goals-approval-btn-cancel">Back</Link>
      </div>
    )
  }
}

CoacheeFooter.propTypes = {
  is_leader: React.PropTypes.bool
}

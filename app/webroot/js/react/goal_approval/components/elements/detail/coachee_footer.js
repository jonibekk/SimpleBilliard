import React from 'react'
import { Link } from 'react-router'

export class CoacheeFooter extends React.Component {
  render() {
    return (
      <div className="goals-approval-detail-choice">
          <Link to="" className="btn goals-approval-btn-fullsize-active">Edit Goal</Link>
          <Link to="/goals/approval/list" className="btn goals-approval-btn-cancel">Back</Link>
      </div>
    )
  }
}

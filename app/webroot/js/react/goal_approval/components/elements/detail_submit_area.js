import React from 'react'
import { Link } from 'react-router'

export class ApproveSubmitArea extends React.Component {
  render() {
    return (
      <div className="goals-approval-detail-comments">
          <label className="goals-approval-input-label" htmlFor="">Do you think this tKR is clear?</label>
          <label className="btn goals-approval-btn-choice" htmlFor="">Clear</label>
          <label className="btn goals-approval-btn-choice" htmlFor="">Not Clear</label>

          <label className="goals-approval-input-label" htmlFor="">Do you think that tKR is the most important to achieve the goal?</label>
          <label className="btn goals-approval-btn-choice" htmlFor="">Yes</label>
          <label className="btn goals-approval-btn-choiced" htmlFor="">No</label>

          <label className="goals-approval-input-label" htmlFor="">Judge this goal to set as target of evaluations.</label>
          <textarea className="form-control goals-approval-detail-input-comment-form" name="" id="" cols="30" rows="2" placeholder="Add your comment (optional)"></textarea>


          <a className="btn goals-approval-btn-nonactive">Set as target</a>
          <a className="btn goals-approval-btn-active">Remove from target</a>
          <a className="btn goals-approval-btn-cancel">Cancel</a>
      </div>
    )
  }
}

ApproveSubmitArea.propTypes = {
}

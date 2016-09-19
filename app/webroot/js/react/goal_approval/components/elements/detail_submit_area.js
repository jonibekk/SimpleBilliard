import React from 'react'
import { Link } from 'react-router'

export class ApproveSubmitArea extends React.Component {
  render() {
    return (
      <div className="goals-approval-detail-comments">
          {(() => {
            if(this.props.is_mine) {
              return (
                <div className="goals-approval-detail-choice">
                    <Link to="" className="btn goals-approval-btn-fullsize-active">Edit Goal</Link>
                    <Link to="/goals/approval/list" className="btn goals-approval-btn-cancel">Back</Link>
                </div>
              )
            } else {
              return (
                <div>
                  <label className="goals-approval-input-label" htmlFor="">Do you think this tKR is clear?</label>
                  <label className="btn goals-approval-btn-choice" htmlFor="">Clear</label>
                  <label className="btn goals-approval-btn-choice" htmlFor="">Not Clear</label>

                  <label className="goals-approval-input-label" htmlFor="">Do you think that tKR is the most important to achieve the goal?</label>
                  <label className="btn goals-approval-btn-choice" htmlFor="">Yes</label>
                  <label className="btn goals-approval-btn-choiced" htmlFor="">No</label>

                  <label className="goals-approval-input-label" htmlFor="">Judge this goal to set as target of evaluations.</label>
                  <textarea className="form-control goals-approval-detail-input-comment-form" name="" id="" cols="30" rows="2" placeholder="Add your comment (optional)"></textarea>
                  <div className="goals-approval-detail-choice">
                      <a className="btn goals-approval-btn-nonactive">Set as target</a>
                      <a className="btn goals-approval-btn-active">Remove from target</a>
                      <Link to="/goals/approval/list" className="btn goals-approval-btn-cancel">Cancel</Link>
                  </div>
                </div>
              )
            }
          })()}
      </div>
    )
  }
}

ApproveSubmitArea.propTypes = {
}
